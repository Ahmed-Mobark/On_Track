"use client";

import { motion } from "framer-motion";
import Image from "next/image";
import Link from "next/link";
import { ArrowRight } from "lucide-react";

export function Hero() {
  return (
    <section className="relative h-[90vh] lg:h-screen bg-brand-black overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-brand-black via-brand-dark to-brand-black" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand-red/10 rounded-full blur-[150px]" />

      {/* Large watermark logo on right side */}
      <div className="absolute right-[-5%] top-1/2 -translate-y-1/2 opacity-[0.03] hidden lg:block">
        <Image
          src="/images/brand/02_ontrack_primary_white.svg"
          alt=""
          width={800}
          height={320}
          className="w-[800px] h-auto"
          aria-hidden="true"
        />
      </div>

      <div className="relative mx-auto max-w-7xl px-4 h-full flex items-center">
        <div className="max-w-2xl">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
          >
            <span className="inline-block text-brand-red font-heading font-semibold text-sm uppercase tracking-[0.2em] mb-4">
              كوليكشن جديد 2026
            </span>
            <h1 className="text-5xl md:text-7xl lg:text-8xl font-heading font-black text-white leading-[0.9] mb-6">
              تخطى{" "}
              <span className="text-gradient">حدودك</span>
            </h1>
            <p className="text-white/60 text-lg md:text-xl mb-8 max-w-md">
              ملابس رياضية بريميوم مصممة للأداء. صُنعت لمن لا يتوقفون عن الحركة.
            </p>
            <div className="flex flex-col sm:flex-row gap-4">
              <Link
                href="/shop"
                className="inline-flex items-center justify-center gap-2 bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-8 py-4 rounded-lg transition-all group text-lg"
              >
                تسوق الآن
                <ArrowRight size={20} className="group-hover:translate-x-1 transition-transform" />
              </Link>
              <Link
                href="/shop?sort=newest"
                className="inline-flex items-center justify-center gap-2 border border-white/20 hover:border-white/40 text-white font-semibold px-8 py-4 rounded-lg transition-colors text-lg"
              >
                وصل حديثاً
              </Link>
            </div>
          </motion.div>
        </div>
      </div>

      <motion.div
        className="absolute bottom-8 left-1/2 -translate-x-1/2"
        animate={{ y: [0, 8, 0] }}
        transition={{ repeat: Infinity, duration: 2 }}
      >
        <div className="w-6 h-10 border-2 border-white/30 rounded-full flex items-start justify-center p-1">
          <div className="w-1.5 h-3 bg-white/50 rounded-full" />
        </div>
      </motion.div>
    </section>
  );
}
