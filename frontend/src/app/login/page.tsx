"use client";

import Link from "next/link";
import Image from "next/image";
import { useState } from "react";
import { Button } from "@/components/ui/button";

export default function LoginPage() {
  const [isLogin, setIsLogin] = useState(true);

  return (
    <div className="bg-brand-black min-h-screen flex items-center justify-center px-4" dir="rtl">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <Link href="/">
            <Image
              src="/images/brand/02_ontrack_primary_white.svg"
              alt="On Track"
              width={160}
              height={64}
              className="h-14 w-auto mx-auto mb-6"
            />
          </Link>
          <h1 className="text-2xl font-heading font-bold text-white">
            {isLogin ? "تسجيل الدخول" : "إنشاء حساب جديد"}
          </h1>
          <p className="text-white/50 mt-2">
            {isLogin ? "مرحباً بعودتك!" : "انضم لعائلة On Track"}
          </p>
        </div>

        <div className="bg-brand-dark rounded-2xl p-8">
          <div className="space-y-4">
            {!isLogin && (
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-white/70 text-sm font-medium mb-1.5">الاسم الأول</label>
                  <input
                    type="text"
                    className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                    placeholder="أحمد"
                  />
                </div>
                <div>
                  <label className="block text-white/70 text-sm font-medium mb-1.5">الاسم الأخير</label>
                  <input
                    type="text"
                    className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                    placeholder="محمد"
                  />
                </div>
              </div>
            )}

            <div>
              <label className="block text-white/70 text-sm font-medium mb-1.5">البريد الإلكتروني</label>
              <input
                type="email"
                className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                placeholder="email@example.com"
                dir="ltr"
              />
            </div>

            {!isLogin && (
              <div>
                <label className="block text-white/70 text-sm font-medium mb-1.5">رقم الموبايل</label>
                <input
                  type="tel"
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                  placeholder="01xxxxxxxxx"
                  dir="ltr"
                />
              </div>
            )}

            <div>
              <label className="block text-white/70 text-sm font-medium mb-1.5">كلمة المرور</label>
              <input
                type="password"
                className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                placeholder="••••••••"
              />
            </div>

            {isLogin && (
              <div className="text-left">
                <button className="text-brand-red text-sm hover:underline">نسيت كلمة المرور؟</button>
              </div>
            )}

            <Button className="w-full bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-4 rounded-xl text-lg">
              {isLogin ? "تسجيل الدخول" : "إنشاء حساب"}
            </Button>
          </div>

          <div className="mt-6 text-center">
            <span className="text-white/40 text-sm">
              {isLogin ? "مالكش حساب؟ " : "عندك حساب؟ "}
            </span>
            <button
              onClick={() => setIsLogin(!isLogin)}
              className="text-brand-red text-sm font-semibold hover:underline"
            >
              {isLogin ? "سجل دلوقتي" : "سجل دخول"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
