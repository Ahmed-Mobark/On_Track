import { Hero } from "@/components/home/hero";
import { CategoriesSection } from "@/components/home/categories-section";
import { ProductsSection } from "@/components/home/products-section";

const mockProducts = Array.from({ length: 8 }, (_, i) => ({
  id: `${i + 1}`,
  name: [
    "تيشيرت رياضي بريميوم",
    "شورت أداء عالي",
    "هودي أوفرسايز",
    "بنطلون جوجر سليم",
    "تانك توب",
    "توب ضغط رياضي",
    "بنطلون تراك",
    "سبورتس برا",
  ][i],
  slug: `product-${i + 1}`,
  basePrice: [299, 349, 599, 499, 249, 399, 449, 329][i],
  salePrice:
    i % 3 === 0
      ? [249, 0, 0, 399, 0, 0, 0, 279][i] || undefined
      : undefined,
  images: [] as { url: string; alt?: string }[],
}));

export default function Home() {
  return (
    <div className="bg-brand-black">
      <Hero />
      <CategoriesSection />
      <ProductsSection
        title="الأكثر"
        highlight="مبيعاً"
        products={mockProducts.slice(0, 4)}
        viewAllHref="/shop?sort=popular"
      />
      <ProductsSection
        title="وصل"
        highlight="حديثاً"
        products={mockProducts.slice(4)}
        viewAllHref="/shop?sort=newest"
      />

      <section className="py-16 border-t border-white/10">
        <div className="mx-auto max-w-7xl px-4">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {[
              { title: "شحن مجاني", desc: "للطلبات فوق 500 ج.م" },
              { title: "إرجاع سهل", desc: "خلال 14 يوم" },
              { title: "دفع آمن", desc: "100% حماية" },
              {
                title: "جودة بريميوم",
                desc: "مصممة للأداء",
              },
            ].map((feature) => (
              <div key={feature.title} className="text-center">
                <h3 className="text-white font-heading font-semibold mb-1">
                  {feature.title}
                </h3>
                <p className="text-white/40 text-sm">{feature.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
