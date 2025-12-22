/**
 * Authentication Composable
 * Handles user authentication state and operations
 */

import type { ApiResponse } from './useApi'

export interface User {
  id: number
  email: string
  firstName: string
  lastName: string
  role: 'customer' | 'seller'
  address?: Address | null
}

export interface Address {
  id?: number
  street: string
  house_number: string
  city: string
  postal_code: string
  country: string
  is_default?: boolean
}

interface LoginResponse {
  user: User
  token: string
}

interface RegisterData {
  email: string
  password: string
  firstName: string
  lastName: string
  role: string
  address: Omit<Address, 'id' | 'is_default'>
}

const TOKEN_KEY = 'token'
const USER_KEY = 'user'

export function useAuth() {
  const user = useState<User | null>('auth-user', () => null)
  const isAuthenticated = computed(() => user.value !== null)
  const isSeller = computed(() => user.value?.role === 'seller')
  const isCustomer = computed(() => user.value?.role === 'customer')

  const api = useApi()

  /**
   * Login user with email and password
   */
  async function login(email: string, password: string): Promise<ApiResponse<LoginResponse>> {
    const response = await api.post<LoginResponse>('/login.php', { email, password })

    if (response.success && response.data) {
      setAuthState(response.data.user, response.data.token)
    }

    return response
  }

  /**
   * Register a new user
   */
  async function register(data: RegisterData): Promise<ApiResponse<LoginResponse>> {
    const response = await api.post<LoginResponse>('/register.php', data)

    if (response.success && response.data) {
      setAuthState(response.data.user, response.data.token)
    }

    return response
  }

  /**
   * Logout current user
   */
  async function logout(): Promise<void> {
    try {
      await api.post('/logout.php')
    } finally {
      // Always clear local state, even if server request fails
      clearAuthState()
    }
  }

  /**
   * Set authentication state in memory and localStorage
   */
  function setAuthState(userData: User, token: string): void {
    user.value = userData
    
    if (import.meta.client) {
      localStorage.setItem(TOKEN_KEY, token)
      localStorage.setItem(USER_KEY, JSON.stringify(userData))
    }
  }

  /**
   * Clear authentication state
   */
  function clearAuthState(): void {
    user.value = null
    
    if (import.meta.client) {
      localStorage.removeItem(TOKEN_KEY)
      localStorage.removeItem(USER_KEY)
    }
  }

  /**
   * Restore auth state from localStorage (called on app init)
   */
  function restoreAuthState(): void {
    if (!import.meta.client) return

    try {
      const storedUser = localStorage.getItem(USER_KEY)
      const storedToken = localStorage.getItem(TOKEN_KEY)

      if (storedUser && storedToken) {
        user.value = JSON.parse(storedUser)
      }
    } catch {
      // Invalid stored data, clear it
      clearAuthState()
    }
  }

  /**
   * Check if current token is still valid
   */
  async function validateToken(): Promise<boolean> {
    if (!import.meta.client) return false

    const token = localStorage.getItem(TOKEN_KEY)
    if (!token) return false

    try {
      const response = await api.get('/users.php', { user_id: user.value?.id })
      return response.success
    } catch {
      return false
    }
  }

  /**
   * Update stored user data
   */
  function updateUser(userData: Partial<User>): void {
    if (user.value) {
      user.value = { ...user.value, ...userData }
      
      if (import.meta.client) {
        localStorage.setItem(USER_KEY, JSON.stringify(user.value))
      }
    }
  }

  /**
   * Get the current auth token
   */
  function getToken(): string | null {
    if (import.meta.client) {
      return localStorage.getItem(TOKEN_KEY)
    }
    return null
  }

  return {
    // State
    user,
    isAuthenticated,
    isSeller,
    isCustomer,
    
    // Actions
    login,
    register,
    logout,
    restoreAuthState,
    validateToken,
    updateUser,
    getToken,
    clearAuthState,
  }
}
