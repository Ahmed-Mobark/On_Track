import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { OrderStatus, PaymentMethod, ShipmentStatus } from '@prisma/client';

@Injectable()
export class OrdersService {
  constructor(private prisma: PrismaService) {}

  async create(
    userId: string,
    data: {
      addressId: string;
      paymentMethod: PaymentMethod;
      couponCode?: string;
      notes?: string;
    },
  ) {
    const cartItems = await this.prisma.cartItem.findMany({
      where: { userId },
      include: { product: true, variant: true },
    });

    if (!cartItems.length) throw new NotFoundException('Cart is empty');

    let subtotal = 0;
    const items = cartItems.map((item) => {
      const price = Number(item.variant.price || item.product.basePrice);
      subtotal += price * item.quantity;
      return {
        productId: item.productId,
        variantId: item.variantId,
        quantity: item.quantity,
        price,
      };
    });

    let discount = 0;
    let couponId: string | undefined;

    if (data.couponCode) {
      const coupon = await this.prisma.coupon.findUnique({
        where: { code: data.couponCode },
      });
      if (
        coupon &&
        coupon.isActive &&
        (!coupon.expiresAt || coupon.expiresAt > new Date())
      ) {
        couponId = coupon.id;
        if (coupon.type === 'PERCENTAGE')
          discount = subtotal * (Number(coupon.value) / 100);
        if (coupon.type === 'FIXED_AMOUNT') discount = Number(coupon.value);
      }
    }

    const shippingCost = subtotal > 500 ? 0 : 50;
    const total = subtotal - discount + shippingCost;

    const orderNumber = `OT-${Date.now()}-${Math.random().toString(36).substring(2, 7).toUpperCase()}`;

    const order = await this.prisma.order.create({
      data: {
        orderNumber,
        userId,
        addressId: data.addressId,
        paymentMethod: data.paymentMethod,
        subtotal,
        shippingCost,
        discount,
        total,
        couponId,
        notes: data.notes,
        items: { create: items },
      },
      include: { items: { include: { product: true, variant: true } } },
    });

    await this.prisma.cartItem.deleteMany({ where: { userId } });

    for (const item of cartItems) {
      await this.prisma.productVariant.update({
        where: { id: item.variantId },
        data: { quantity: { decrement: item.quantity } },
      });
    }

    return order;
  }

  async findByUser(userId: string, page = 1, limit = 10) {
    const skip = (page - 1) * limit;
    const [orders, total] = await Promise.all([
      this.prisma.order.findMany({
        where: { userId },
        skip,
        take: limit,
        orderBy: { createdAt: 'desc' },
        include: {
          items: {
            include: { product: { include: { images: { take: 1 } } } },
          },
        },
      }),
      this.prisma.order.count({ where: { userId } }),
    ]);
    return {
      data: orders,
      meta: { total, page, limit, totalPages: Math.ceil(total / limit) },
    };
  }

  async findAll(page = 1, limit = 20, status?: OrderStatus) {
    const skip = (page - 1) * limit;
    const where = status ? { status } : {};
    const [orders, total] = await Promise.all([
      this.prisma.order.findMany({
        where,
        skip,
        take: limit,
        orderBy: { createdAt: 'desc' },
        include: {
          user: { select: { firstName: true, lastName: true, email: true } },
          items: { include: { product: true } },
        },
      }),
      this.prisma.order.count({ where }),
    ]);
    return {
      data: orders,
      meta: { total, page, limit, totalPages: Math.ceil(total / limit) },
    };
  }

  async findById(id: string) {
    const order = await this.prisma.order.findUnique({
      where: { id },
      include: {
        user: {
          select: {
            firstName: true,
            lastName: true,
            email: true,
            phone: true,
          },
        },
        address: true,
        items: {
          include: {
            product: { include: { images: { take: 1 } } },
            variant: true,
          },
        },
        coupon: true,
      },
    });
    if (!order) throw new NotFoundException('Order not found');
    return order;
  }

  async updateStatus(id: string, status: OrderStatus) {
    return this.prisma.order.update({ where: { id }, data: { status } });
  }

  async updateShipping(
    id: string,
    data: {
      shippingCompany: string;
      trackingNumber: string;
      shipmentStatus: ShipmentStatus;
    },
  ) {
    return this.prisma.order.update({
      where: { id },
      data: {
        shippingCompany: data.shippingCompany,
        trackingNumber: data.trackingNumber,
        shipmentStatus: data.shipmentStatus,
        status: 'SHIPPED',
      },
    });
  }

  async getStats() {
    const [totalOrders, totalRevenue, pendingOrders, todayOrders] =
      await Promise.all([
        this.prisma.order.count(),
        this.prisma.order.aggregate({
          _sum: { total: true },
          where: { paymentStatus: 'PAID' },
        }),
        this.prisma.order.count({ where: { status: 'PENDING' } }),
        this.prisma.order.count({
          where: {
            createdAt: { gte: new Date(new Date().setHours(0, 0, 0, 0)) },
          },
        }),
      ]);

    return {
      totalOrders,
      totalRevenue: totalRevenue._sum.total || 0,
      pendingOrders,
      todayOrders,
    };
  }
}
