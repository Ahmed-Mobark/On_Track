import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class CartService {
  constructor(private prisma: PrismaService) {}

  async getCart(userId: string) {
    const items = await this.prisma.cartItem.findMany({
      where: { userId },
      include: {
        product: {
          include: { images: { take: 1, orderBy: { sortOrder: 'asc' } } },
        },
        variant: true,
      },
    });

    const subtotal = items.reduce((sum, item) => {
      const price = Number(item.variant.price || item.product.basePrice);
      return sum + price * item.quantity;
    }, 0);

    return {
      items,
      subtotal,
      shippingCost: subtotal > 500 ? 0 : 50,
      total: subtotal + (subtotal > 500 ? 0 : 50),
    };
  }

  async addItem(
    userId: string,
    data: { productId: string; variantId: string; quantity: number },
  ) {
    const variant = await this.prisma.productVariant.findUnique({
      where: { id: data.variantId },
    });
    if (!variant || variant.quantity < data.quantity)
      throw new NotFoundException('Variant not available');

    const existing = await this.prisma.cartItem.findUnique({
      where: {
        userId_productId_variantId: {
          userId,
          productId: data.productId,
          variantId: data.variantId,
        },
      },
    });

    if (existing) {
      await this.prisma.cartItem.update({
        where: { id: existing.id },
        data: { quantity: existing.quantity + data.quantity },
      });
    } else {
      await this.prisma.cartItem.create({
        data: { userId, ...data },
      });
    }

    return this.getCart(userId);
  }

  async updateQuantity(userId: string, itemId: string, quantity: number) {
    if (quantity <= 0) {
      await this.prisma.cartItem.delete({ where: { id: itemId } });
    } else {
      await this.prisma.cartItem.update({
        where: { id: itemId },
        data: { quantity },
      });
    }
    return this.getCart(userId);
  }

  async removeItem(userId: string, itemId: string) {
    await this.prisma.cartItem.delete({ where: { id: itemId } });
    return this.getCart(userId);
  }

  async clearCart(userId: string) {
    await this.prisma.cartItem.deleteMany({ where: { userId } });
    return { items: [], subtotal: 0, shippingCost: 0, total: 0 };
  }
}
