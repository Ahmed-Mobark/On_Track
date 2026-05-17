"use client";

import { motion } from "framer-motion";
import Link from "next/link";

const categories = [
  { name: "Men", slug: "men" },
  { name: "Women", slug: "women" },
  { name: "Oversized", slug: "oversized" },
  { name: "Compression", slug: "compression" },
  { name: "Shorts", slug: "shorts" },
  { name: "Sets", slug: "sets" },
];

export function CategoriesSection() {
  return (
    <section className="py-16 lg:py-24 bg-brand-black">
      <div className="mx-auto max-w-7xl px-4">
        <div className="text-center mb-12">
          <h2 className="text-3xl lg:text-4xl font-heading font-bold text-white mb-2">
            Shop by <span className="text-brand-red">Category</span>
          </h2>
          <p className="text-white/50">Find your perfect fit</p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-6">
          {categories.map((cat, i) => (
            <motion.div
              key={cat.slug}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
            >
              <Link
                href={`/shop?category=${cat.slug}`}
                className="group relative block aspect-[3/4] md:aspect-[4/5] bg-brand-dark rounded-xl overflow-hidden"
              >
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10" />
                <div className="absolute inset-0 bg-brand-gray flex items-center justify-center">
                  <span className="text-white/10 text-8xl font-heading font-black">{cat.name[0]}</span>
                </div>
                <div className="absolute bottom-0 left-0 right-0 p-4 lg:p-6 z-20">
                  <h3 className="text-white font-heading font-bold text-lg lg:text-xl group-hover:text-brand-red transition-colors">
                    {cat.name}
                  </h3>
                  <span className="text-white/50 text-sm group-hover:text-white/70 transition-colors">
                    Shop Now &rarr;
                  </span>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}
