/**
 * Centralized API Client Composable
 * Provides consistent API access with automatic token injection and error handling
 */

import type { FetchError } from 'ofetch'

export interface ApiResponse<T = unknown> {
  success: boolean
  data?: T
  message?: string
  errors?: Record<string, string>
}

export interface ApiError {
  success: false
  message: string
  errors?: Record<string, string>
  statusCode: number
}

/**
 * Get the stored auth token
 */
function getAuthToken(): string | null {
  if (import.meta.client) {
    return localStorage.getItem('token')
  }
  return null
}

/**
 * Handle 401 Unauthorized errors by logging out the user
 */
function handleUnauthorized(): void {
  if (!import.meta.client) return
  
  // Get auth composable
  const { clearAuthState } = useAuth()
  const { clearLocalCart } = useCart()
  const router = useRouter()
  
  // Clear auth state
  clearAuthState()
  clearLocalCart()
  
  // Show notification
  const sessionExpiredEvent = new CustomEvent('session-expired', {
    detail: { message: 'Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.' }
  })
  window.dispatchEvent(sessionExpiredEvent)
  
  // Redirect to login page
  router.push('/login')
}

/**
 * Composable for making API requests with consistent error handling
 */
export function useApi() {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiUrl

  /**
   * Make an API request with automatic auth header injection
   */
  async function request<T = unknown>(
    endpoint: string,
    options: Record<string, unknown> = {}
  ): Promise<ApiResponse<T>> {
    const token = getAuthToken()
    
    const headers: Record<string, string> = {
      ...(options.headers as Record<string, string> || {}),
    }

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    try {
      const response = await $fetch<ApiResponse<T>>(endpoint, {
        baseURL: baseUrl,
        ...options,
        headers,
        credentials: 'include',
      })

      return response
    } catch (error) {
      const fetchError = error as FetchError<ApiResponse<T>>
      
      // Handle 401 Unauthorized - token expired or invalid
      if (fetchError.statusCode === 401) {
        handleUnauthorized()
        return {
          success: false,
          message: 'Sitzung abgelaufen',
        }
      }
      
      // Handle structured API errors
      if (fetchError.data) {
        return {
          success: false,
          message: fetchError.data.message || 'Ein Fehler ist aufgetreten',
          errors: fetchError.data.errors,
        }
      }

      // Handle network or other errors
      return {
        success: false,
        message: fetchError.message || 'Netzwerkfehler',
      }
    }
  }

  /**
   * GET request
   */
  async function get<T = unknown>(
    endpoint: string,
    params?: Record<string, unknown>
  ): Promise<ApiResponse<T>> {
    return request<T>(endpoint, {
      method: 'GET',
      params,
    })
  }

  /**
   * POST request
   */
  async function post<T = unknown>(
    endpoint: string,
    body?: Record<string, unknown> | unknown
  ): Promise<ApiResponse<T>> {
    return request<T>(endpoint, {
      method: 'POST',
      body,
    })
  }

  /**
   * PUT request
   */
  async function put<T = unknown>(
    endpoint: string,
    body?: Record<string, unknown> | unknown
  ): Promise<ApiResponse<T>> {
    return request<T>(endpoint, {
      method: 'PUT',
      body,
    })
  }

  /**
   * DELETE request
   */
  async function del<T = unknown>(
    endpoint: string,
    body?: Record<string, unknown> | unknown
  ): Promise<ApiResponse<T>> {
    return request<T>(endpoint, {
      method: 'DELETE',
      body,
    })
  }

  /**
   * Upload file with multipart form data
   */
  async function upload<T = unknown>(
    endpoint: string,
    file: File,
    fieldName: string = 'image'
  ): Promise<ApiResponse<T>> {
    const formData = new FormData()
    formData.append(fieldName, file)

    const token = getAuthToken()
    const headers: HeadersInit = {}

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    try {
      const response = await $fetch<ApiResponse<T>>(endpoint, {
        baseURL: baseUrl,
        method: 'POST',
        body: formData,
        headers,
        credentials: 'include',
      })

      return response
    } catch (error) {
      const fetchError = error as FetchError<ApiResponse<T>>
      
      // Handle 401 Unauthorized
      if (fetchError.statusCode === 401) {
        handleUnauthorized()
        return {
          success: false,
          message: 'Sitzung abgelaufen',
        }
      }
      
      if (fetchError.data) {
        return {
          success: false,
          message: fetchError.data.message || 'Upload fehlgeschlagen',
          errors: fetchError.data.errors,
        }
      }

      return {
        success: false,
        message: fetchError.message || 'Upload fehlgeschlagen',
      }
    }
  }

  return {
    request,
    get,
    post,
    put,
    del,
    upload,
    getAuthToken,
    baseUrl,
  }
}
