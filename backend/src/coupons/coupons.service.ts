import {
  Injectable,
  NotFoundException,
  BadRequestException,
} from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class CouponsService {
  constructor(private prisma: PrismaService) {}

  async validate(code: string, orderTotal: number) {
    const coupon = await this.prisma.coupon.findUnique({ where: { code } });
    if (!coupon) throw new NotFoundException('Coupon not found');
    if (!coupon.isActive)
      throw new BadRequestException('Coupon is not active');
    if (coupon.expiresAt && coupon.expiresAt < new Date())
      throw new BadRequestException('Coupon has expired');
    if (coupon.maxUses && coupon.usedCount >= coupon.maxUses)
      throw new BadRequestException('Coupon usage limit reached');
    if (coupon.minOrderValue && orderTotal < Number(coupon.minOrderValue)) {
      throw new BadRequestException(
        `Minimum order value is ${coupon.minOrderValue} EGP`,
      );
    }

    let discount = 0;
    if (coupon.type === 'PERCENTAGE')
      discount = orderTotal * (Number(coupon.value) / 100);
    if (coupon.type === 'FIXED_AMOUNT') discount = Number(coupon.value);

    return {
      valid: true,
      coupon,
      discount,
      freeShipping: coupon.type === 'FREE_SHIPPING',
    };
  }

  async findAll() {
    return this.prisma.coupon.findMany({ orderBy: { createdAt: 'desc' } });
  }

  async create(data: any) {
    return this.prisma.coupon.create({ data });
  }

  async update(id: string, data: any) {
    return this.prisma.coupon.update({ where: { id }, data });
  }

  async delete(id: string) {
    return this.prisma.coupon.delete({ where: { id } });
  }
}
