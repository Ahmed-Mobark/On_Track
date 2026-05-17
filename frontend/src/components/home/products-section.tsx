"use client";

import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { ProductCard } from "./product-card";

interface ProductsSectionProps {
  title: string;
  highlight: string;
  products: any[];
  viewAllHref: string;
}

export function ProductsSection({ title, highlight, products, viewAllHref }: ProductsSectionProps) {
  return (
    <section className="py-16 lg:py-24">
      <div className="mx-auto max-w-7xl px-4">
        <div className="flex items-end justify-between mb-10">
          <h2 className="text-3xl lg:text-4xl font-heading font-bold text-white">
            {title} <span className="text-brand-red">{highlight}</span>
          </h2>
          <Link
            href={viewAllHref}
            className="hidden sm:flex items-center gap-1 text-white/60 hover:text-brand-red text-sm font-medium transition-colors"
          >
            View All <ArrowRight size={16} />
          </Link>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
          {products.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>

        <div className="sm:hidden mt-8 text-center">
          <Link href={viewAllHref} className="inline-flex items-center gap-1 text-brand-red font-medium">
            View All <ArrowRight size={16} />
          </Link>
        </div>
      </div>
    </section>
  );
}
