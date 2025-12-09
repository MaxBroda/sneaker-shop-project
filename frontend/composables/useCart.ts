import { ref, computed } from 'vue';

export interface CartItem {
  id: number;
  product_id: number;
  name: string;
  price: number;
  image: string;
  quantity: number;
  size: string;
  category?: string;
}

const cartItems = ref<CartItem[]>([]);
const isLoading = ref(false);

export const useCart = () => {
  const config = useRuntimeConfig();
  const apiUrl = config.public.apiUrl || 'http://localhost:8080/api';

  const cartItemCount = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
  });

  const cartTotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  });

  const getAuthToken = () => {
    if (import.meta.client) {
      const token = localStorage.getItem('authToken') || localStorage.getItem('token');
      console.log('[CART] Getting auth token:', token ? 'Found' : 'Not found');
      return token;
    }
    return null;
  };

  const fetchCart = async () => {
    try {
      isLoading.value = true;
      const token = getAuthToken();
      
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiUrl}/cart.php`, {
        method: 'GET',
        headers,
        credentials: 'include',
      });

      const data = await response.json();
      
      if (data.success) {
        cartItems.value = data.items;
      }
    } catch (error) {
      console.error('Error fetching cart:', error);
    } finally {
      isLoading.value = false;
    }
  };

  const addToCart = async (productId: number, size: string, quantity: number = 1) => {
    try {
      isLoading.value = true;
      const token = getAuthToken();
      
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiUrl}/cart.php`, {
        method: 'POST',
        headers,
        credentials: 'include',
        body: JSON.stringify({
          product_id: productId,
          size,
          quantity,
        }),
      });

      const data = await response.json();
      
      if (data.success) {
        cartItems.value = data.items;
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Error adding to cart:', error);
      return { success: false, message: 'Fehler beim Hinzufügen zum Warenkorb' };
    } finally {
      isLoading.value = false;
    }
  };

  const updateQuantity = async (cartItemId: number, quantity: number) => {
    try {
      isLoading.value = true;
      const token = getAuthToken();
      
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiUrl}/cart.php`, {
        method: 'PUT',
        headers,
        credentials: 'include',
        body: JSON.stringify({
          cart_item_id: cartItemId,
          quantity,
        }),
      });

      const data = await response.json();
      
      if (data.success) {
        cartItems.value = data.items;
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Error updating quantity:', error);
      return { success: false, message: 'Fehler beim Aktualisieren der Menge' };
    } finally {
      isLoading.value = false;
    }
  };

  const removeFromCart = async (cartItemId: number) => {
    try {
      isLoading.value = true;
      const token = getAuthToken();
      
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiUrl}/cart.php`, {
        method: 'DELETE',
        headers,
        credentials: 'include',
        body: JSON.stringify({
          cart_item_id: cartItemId,
        }),
      });

      const data = await response.json();
      
      if (data.success) {
        cartItems.value = data.items;
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Error removing from cart:', error);
      return { success: false, message: 'Fehler beim Entfernen aus dem Warenkorb' };
    } finally {
      isLoading.value = false;
    }
  };

  const increaseQuantity = async (cartItemId: number) => {
    const item = cartItems.value.find(i => i.id === cartItemId);
    if (item) {
      return await updateQuantity(cartItemId, item.quantity + 1);
    }
  };

  const decreaseQuantity = async (cartItemId: number) => {
    const item = cartItems.value.find(i => i.id === cartItemId);
    if (item && item.quantity > 1) {
      return await updateQuantity(cartItemId, item.quantity - 1);
    } else if (item && item.quantity === 1) {
      return await removeFromCart(cartItemId);
    }
  };

  const mergeCart = async () => {
    await fetchCart();
  };

  const formatPrice = (price: number): string => {
    return `${price.toFixed(2)} €`;
  };

  const clearCart = async () => {
    const config = useRuntimeConfig();
    const { token } = useAuth();

    cartItems.value = [];

    if (token.value) {
      try {
        await $fetch(`${config.public.apiUrl}/cart.php`, {
          method: 'DELETE',
          headers: {
            Authorization: `Bearer ${token.value || localStorage.getItem('token')}`,
          },
        });
      } catch (error) {
        console.error('Error clearing backend cart:', error);
      }
    }
  };

  return {
    cartItems,
    cartItemCount,
    cartTotal,
    isLoading,
    fetchCart,
    addToCart,
    updateQuantity,
    removeFromCart,
    increaseQuantity,
    decreaseQuantity,
    mergeCart,
    formatPrice,
    clearCart,
  };
};
