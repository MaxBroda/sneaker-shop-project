/**
 * Auth Client Plugin
 * Restores authentication state on client-side initialization
 */

export default defineNuxtPlugin(async () => {
  const { restoreAuthState, validateToken, clearAuthState, user } = useAuth()
  const { fetchCart, clearLocalCart } = useCart()

  // Restore auth state from localStorage
  restoreAuthState()

  // If user is logged in, validate the token
  if (user.value) {
    const isValid = await validateToken()
    
    if (!isValid) {
      // Token is invalid or expired, clear state
      clearAuthState()
      clearLocalCart()
    } else {
      // Token is valid, fetch cart
      await fetchCart()
    }
  }
})
