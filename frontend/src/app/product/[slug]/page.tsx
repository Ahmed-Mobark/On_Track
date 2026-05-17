import ProductDetail from "./product-detail";

export function generateStaticParams() {
  return Array.from({ length: 8 }, (_, i) => ({
    slug: `product-${i + 1}`,
  }));
}

export default async function ProductPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  return <ProductDetail slug={slug} />;
}
