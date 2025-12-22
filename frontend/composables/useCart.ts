/**
 * Cart Composable
 * Handles shopping cart state and operations
 */

import type { ApiResponse } from './useApi'

export interface CartItem {
  id: number
  product_id: number
  quantity: number
  size: string
  name: string
  price: number
  image: string | null
  category: string | null
}

interface CartResponse {
  items: CartItem[]
  count: number
}

export function useCart() {
  const items = useState<CartItem[]>('cart-items', () => [])
  const isLoading = useState<boolean>('cart-loading', () => false)
  const error = useState<string | null>('cart-error', () => null)

  const api = useApi()
  const config = useRuntimeConfig()

  const itemCount = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const total = computed(() => {
    return items.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
  })

  /**
   * Format image URL with uploads base path
   */
  function formatImageUrl(image: string | null): string | null {
    if (!image) return null
    if (image.startsWith('http')) return image
    return `${config.public.uploadsUrl}/${image}`
  }

  /**
   * Fetch cart from server
   */
  async function fetchCart(): Promise<void> {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get<CartResponse>('/cart.php')

      if (response.success && response.data) {
        items.value = response.data.items.map(item => ({
          ...item,
          image: formatImageUrl(item.image)
        }))
      } else {
        error.value = response.message || 'Fehler beim Laden des Warenkorbs'
      }
    } catch {
      error.value = 'Netzwerkfehler beim Laden des Warenkorbs'
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Add item to cart
   */
  async function addItem(
    productId: number,
    size: string,
    quantity: number = 1
  ): Promise<{ success: boolean; message: string }> {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.post<CartResponse>('/cart.php', {
        product_id: productId,
        size,
        quantity,
      })

      if (response.success && response.data) {
        items.value = response.data.items.map(item => ({
          ...item,
          image: formatImageUrl(item.image)
        }))
        return { success: true, message: response.message || 'Zum Warenkorb hinzugefügt' }
      } else {
        return { success: false, message: response.message || 'Fehler beim Hinzufügen' }
      }
    } catch {
      return { success: false, message: 'Netzwerkfehler' }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Update item quantity
   */
  async function updateQuantity(
    cartItemId: number,
    quantity: number
  ): Promise<{ success: boolean; message: string }> {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.put<CartResponse>('/cart.php', {
        cart_item_id: cartItemId,
        quantity,
      })

      if (response.success && response.data) {
        items.value = response.data.items.map(item => ({
          ...item,
          image: formatImageUrl(item.image)
        }))
        return { success: true, message: response.message || 'Menge aktualisiert' }
      } else {
        return { success: false, message: response.message || 'Fehler beim Aktualisieren' }
      }
    } catch {
      return { success: false, message: 'Netzwerkfehler' }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Remove item from cart
   */
  async function removeItem(cartItemId: number): Promise<{ success: boolean; message: string }> {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.del<CartResponse>('/cart.php', {
        cart_item_id: cartItemId,
      })

      if (response.success && response.data) {
        items.value = response.data.items.map(item => ({
          ...item,
          image: formatImageUrl(item.image)
        }))
        return { success: true, message: response.message || 'Artikel entfernt' }
      } else {
        return { success: false, message: response.message || 'Fehler beim Entfernen' }
      }
    } catch {
      return { success: false, message: 'Netzwerkfehler' }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Clear entire cart
   */
  async function clearCart(): Promise<{ success: boolean; message: string }> {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.del<CartResponse>('/cart.php', {})

      if (response.success) {
        items.value = []
        return { success: true, message: response.message || 'Warenkorb geleert' }
      } else {
        return { success: false, message: response.message || 'Fehler beim Leeren' }
      }
    } catch {
      return { success: false, message: 'Netzwerkfehler' }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Clear local cart state (used on logout)
   */
  function clearLocalCart(): void {
    items.value = []
    error.value = null
  }

  return {
    // State
    items,
    isLoading,
    error,
    itemCount,
    total,

    // Actions
    fetchCart,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    clearLocalCart,
    formatImageUrl,
  }
}
