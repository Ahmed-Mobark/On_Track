import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { Prisma } from '@prisma/client';

@Injectable()
export class ProductsService {
  constructor(private prisma: PrismaService) {}

  async findAll(params: {
    page?: number;
    limit?: number;
    category?: string;
    gender?: string;
    minPrice?: number;
    maxPrice?: number;
    color?: string;
    size?: string;
    sort?: string;
    search?: string;
  }) {
    const {
      page = 1,
      limit = 12,
      category,
      gender,
      minPrice,
      maxPrice,
      color,
      size,
      sort,
      search,
    } = params;
    const skip = (page - 1) * limit;

    const where: Prisma.ProductWhereInput = { isActive: true };

    if (search) {
      where.OR = [
        { name: { contains: search, mode: 'insensitive' } },
        { description: { contains: search, mode: 'insensitive' } },
      ];
    }
    if (category) {
      where.categories = { some: { category: { slug: category } } };
    }
    if (gender) where.gender = gender;
    if (minPrice || maxPrice) {
      where.basePrice = {};
      if (minPrice) where.basePrice.gte = minPrice;
      if (maxPrice) where.basePrice.lte = maxPrice;
    }
    if (color || size) {
      const variantFilter: any = {};
      if (color)
        variantFilter.color = { contains: color, mode: 'insensitive' };
      if (size) variantFilter.size = size;
      where.variants = { some: variantFilter };
    }

    let orderBy: Prisma.ProductOrderByWithRelationInput = {
      createdAt: 'desc',
    };
    if (sort === 'price_asc') orderBy = { basePrice: 'asc' };
    if (sort === 'price_desc') orderBy = { basePrice: 'desc' };
    if (sort === 'popular') orderBy = { totalSold: 'desc' };

    const [products, total] = await Promise.all([
      this.prisma.product.findMany({
        where,
        skip,
        take: limit,
        orderBy,
        include: {
          images: { orderBy: { sortOrder: 'asc' }, take: 2 },
          categories: { include: { category: true } },
          variants: true,
        },
      }),
      this.prisma.product.count({ where }),
    ]);

    return {
      data: products,
      meta: { total, page, limit, totalPages: Math.ceil(total / limit) },
    };
  }

  async findBySlug(slug: string) {
    const product = await this.prisma.product.findUnique({
      where: { slug },
      include: {
        images: { orderBy: { sortOrder: 'asc' } },
        categories: { include: { category: true } },
        variants: true,
        reviews: {
          include: {
            user: {
              select: { firstName: true, lastName: true, avatar: true },
            },
          },
          orderBy: { createdAt: 'desc' },
          take: 10,
        },
        tags: true,
      },
    });
    if (!product) throw new NotFoundException('Product not found');
    return product;
  }

  async create(data: any) {
    const { categories, images, variants, tags, ...productData } = data;

    return this.prisma.product.create({
      data: {
        ...productData,
        categories: categories
          ? {
              create: categories.map((catId: string) => ({
                categoryId: catId,
              })),
            }
          : undefined,
        images: images ? { create: images } : undefined,
        variants: variants ? { create: variants } : undefined,
        tags: tags
          ? { create: tags.map((t: string) => ({ tag: t })) }
          : undefined,
      },
      include: {
        images: true,
        categories: { include: { category: true } },
        variants: true,
        tags: true,
      },
    });
  }

  async update(id: string, data: any) {
    const { categories, ...productData } = data;

    if (categories) {
      await this.prisma.productCategory.deleteMany({
        where: { productId: id },
      });
      await this.prisma.productCategory.createMany({
        data: categories.map((catId: string) => ({
          productId: id,
          categoryId: catId,
        })),
      });
    }

    return this.prisma.product.update({
      where: { id },
      data: productData,
      include: {
        images: true,
        categories: { include: { category: true } },
        variants: true,
        tags: true,
      },
    });
  }

  async delete(id: string) {
    return this.prisma.product.delete({ where: { id } });
  }

  async getBestSellers(limit = 8) {
    return this.prisma.product.findMany({
      where: { isActive: true, isBestSeller: true },
      take: limit,
      orderBy: { totalSold: 'desc' },
      include: { images: { take: 1, orderBy: { sortOrder: 'asc' } } },
    });
  }

  async getNewArrivals(limit = 8) {
    return this.prisma.product.findMany({
      where: { isActive: true },
      take: limit,
      orderBy: { createdAt: 'desc' },
      include: { images: { take: 1, orderBy: { sortOrder: 'asc' } } },
    });
  }

  async getFeatured(limit = 8) {
    return this.prisma.product.findMany({
      where: { isActive: true, isFeatured: true },
      take: limit,
      include: { images: { take: 1, orderBy: { sortOrder: 'asc' } } },
    });
  }
}
