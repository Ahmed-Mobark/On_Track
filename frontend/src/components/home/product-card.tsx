"use client";

import Image from "next/image";
import Link from "next/link";
import { Heart } from "lucide-react";
import { motion } from "framer-motion";

interface ProductCardProps {
  product: {
    id: string;
    name: string;
    slug: string;
    basePrice: number;
    salePrice?: number;
    images: { url: string; alt?: string }[];
  };
}

export function ProductCard({ product }: ProductCardProps) {
  const hasDiscount = product.salePrice && product.salePrice < product.basePrice;
  const discount = hasDiscount
    ? Math.round(((product.basePrice - product.salePrice!) / product.basePrice) * 100)
    : 0;

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      className="group"
    >
      <Link href={`/product/${product.slug}`} className="block">
        <div className="relative aspect-[3/4] bg-brand-dark rounded-xl overflow-hidden mb-3">
          {product.images[0] ? (
            <Image
              src={product.images[0].url}
              alt={product.images[0].alt || product.name}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-500"
            />
          ) : (
            <div className="w-full h-full bg-brand-gray flex items-center justify-center">
              <span className="text-white/20 text-4xl font-heading font-black">OT</span>
            </div>
          )}

          {hasDiscount && (
            <span className="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded">
              -{discount}%
            </span>
          )}

          <button
            className="absolute top-3 right-3 w-8 h-8 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center text-white/70 hover:text-brand-red hover:bg-white/20 transition-all opacity-0 group-hover:opacity-100"
            onClick={(e) => e.preventDefault()}
          >
            <Heart size={16} />
          </button>

          <div className="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform">
            <button
              className="w-full bg-brand-red hover:bg-brand-red-dark text-white text-sm font-semibold py-2.5 rounded-lg transition-colors"
              onClick={(e) => e.preventDefault()}
            >
              Quick Add
            </button>
          </div>
        </div>

        <h3 className="text-white font-medium text-sm lg:text-base mb-1 group-hover:text-brand-red transition-colors line-clamp-1">
          {product.name}
        </h3>
        <div className="flex items-center gap-2">
          {hasDiscount ? (
            <>
              <span className="text-brand-red font-bold">{product.salePrice} EGP</span>
              <span className="text-white/40 line-through text-sm">{product.basePrice} EGP</span>
            </>
          ) : (
            <span className="text-white font-bold">{product.basePrice} EGP</span>
          )}
        </div>
      </Link>
    </motion.div>
  );
}
