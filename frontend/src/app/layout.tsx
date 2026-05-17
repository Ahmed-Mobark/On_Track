import type { Metadata } from "next";
import { Inter, Poppins } from "next/font/google";
import "./globals.css";
import { Header } from "@/components/layout/header";
import { Footer } from "@/components/layout/footer";
import { MobileNav } from "@/components/layout/mobile-nav";
import { QueryProvider } from "@/providers/query-provider";
import { Toaster } from "react-hot-toast";

const inter = Inter({ subsets: ["latin"], variable: "--font-inter" });
const poppins = Poppins({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700", "800", "900"],
  variable: "--font-poppins",
});

export const metadata: Metadata = {
  title: "On Track | Premium Sportswear",
  description:
    "Premium sportswear engineered for performance. Designed for those who never stop moving.",
  keywords: [
    "sportswear",
    "activewear",
    "gym",
    "fitness",
    "egypt",
    "on track",
  ],
  openGraph: {
    title: "On Track | Premium Sportswear",
    description: "Premium sportswear engineered for performance.",
    type: "website",
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className={`${inter.variable} ${poppins.variable} dark`}>
      <body className="font-sans antialiased">
        <QueryProvider>
          <Header />
          <main className="min-h-screen pb-16 lg:pb-0">{children}</main>
          <Footer />
          <MobileNav />
          <Toaster
            position="top-center"
            toastOptions={{
              style: {
                background: "#1A1A1A",
                color: "#fff",
                border: "1px solid rgba(255,255,255,0.1)",
              },
            }}
          />
        </QueryProvider>
      </body>
    </html>
  );
}
