import { create } from 'zustand';
import api from '@/lib/api';

interface CartItem {
  id: string;
  productId: string;
  variantId: string;
  quantity: number;
  product: {
    id: string;
    name: string;
    slug: string;
    basePrice: number;
    salePrice?: number;
    images: { url: string }[];
  };
  variant: {
    id: string;
    size: string;
    color: string;
    price?: number;
  };
}

interface CartState {
  items: CartItem[];
  subtotal: number;
  shippingCost: number;
  total: number;
  isLoading: boolean;
  fetchCart: () => Promise<void>;
  addItem: (productId: string, variantId: string, quantity: number) => Promise<void>;
  updateQuantity: (itemId: string, quantity: number) => Promise<void>;
  removeItem: (itemId: string) => Promise<void>;
  clearCart: () => Promise<void>;
}

export const useCartStore = create<CartState>((set) => ({
  items: [],
  subtotal: 0,
  shippingCost: 0,
  total: 0,
  isLoading: false,

  fetchCart: async () => {
    set({ isLoading: true });
    try {
      const { data } = await api.get('/cart');
      set({ items: data.items, subtotal: data.subtotal, shippingCost: data.shippingCost, total: data.total });
    } catch {
      // Not logged in
    } finally {
      set({ isLoading: false });
    }
  },

  addItem: async (productId, variantId, quantity) => {
    const { data } = await api.post('/cart', { productId, variantId, quantity });
    set({ items: data.items, subtotal: data.subtotal, shippingCost: data.shippingCost, total: data.total });
  },

  updateQuantity: async (itemId, quantity) => {
    const { data } = await api.put(`/cart/${itemId}`, { quantity });
    set({ items: data.items, subtotal: data.subtotal, shippingCost: data.shippingCost, total: data.total });
  },

  removeItem: async (itemId) => {
    const { data } = await api.delete(`/cart/${itemId}`);
    set({ items: data.items, subtotal: data.subtotal, shippingCost: data.shippingCost, total: data.total });
  },

  clearCart: async () => {
    const { data } = await api.delete('/cart');
    set({ items: data.items, subtotal: data.subtotal, shippingCost: data.shippingCost, total: data.total });
  },
}));
