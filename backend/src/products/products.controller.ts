import { Controller, Get, Param, Query } from '@nestjs/common';
import { ProductsService } from './products.service';

@Controller('products')
export class ProductsController {
  constructor(private productsService: ProductsService) {}

  @Get()
  findAll(
    @Query('page') page?: string,
    @Query('limit') limit?: string,
    @Query('category') category?: string,
    @Query('gender') gender?: string,
    @Query('minPrice') minPrice?: string,
    @Query('maxPrice') maxPrice?: string,
    @Query('color') color?: string,
    @Query('size') size?: string,
    @Query('sort') sort?: string,
    @Query('search') search?: string,
  ) {
    return this.productsService.findAll({
      page: page ? parseInt(page) : undefined,
      limit: limit ? parseInt(limit) : undefined,
      category,
      gender,
      color,
      size,
      sort,
      search,
      minPrice: minPrice ? parseFloat(minPrice) : undefined,
      maxPrice: maxPrice ? parseFloat(maxPrice) : undefined,
    });
  }

  @Get('best-sellers')
  getBestSellers(@Query('limit') limit?: string) {
    return this.productsService.getBestSellers(
      limit ? parseInt(limit) : undefined,
    );
  }

  @Get('new-arrivals')
  getNewArrivals(@Query('limit') limit?: string) {
    return this.productsService.getNewArrivals(
      limit ? parseInt(limit) : undefined,
    );
  }

  @Get('featured')
  getFeatured(@Query('limit') limit?: string) {
    return this.productsService.getFeatured(
      limit ? parseInt(limit) : undefined,
    );
  }

  @Get(':slug')
  findBySlug(@Param('slug') slug: string) {
    return this.productsService.findBySlug(slug);
  }
}
