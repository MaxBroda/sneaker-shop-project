/**
 * Product Form Composable
 * Shared logic for product creation and editing
 */

export interface ProductFormData {
  name: string
  description: string
  price: string
  category: string[]
  technicalSpecs: string
  tagIcon: string
  tagText: string
  image: string
}

export interface ProductFormState {
  form: ProductFormData
  imageFile: File | null
  isSubmitting: boolean
  uploadProgress: number
  errors: Record<string, string>
}

const AVAILABLE_CATEGORIES = [
  'Running',
  'Lifestyle', 
  'Basketball',
  'Training',
  'Limited Edition'
]

const TAG_OPTIONS = [
  { icon: '🆕', text: 'Neu' },
  { icon: '🔥', text: 'Bestseller' },
  { icon: '🌱', text: 'Nachhaltig' },
  { icon: '⚡', text: 'Limitiert' },
  { icon: '💎', text: 'Premium' },
  { icon: '🎯', text: 'Top Seller' },
]

export function useProductForm(initialProduct?: Partial<ProductFormData>) {
  const api = useApi()
  
  const state = reactive<ProductFormState>({
    form: {
      name: initialProduct?.name || '',
      description: initialProduct?.description || '',
      price: initialProduct?.price || '',
      category: initialProduct?.category ? initialProduct.category.split(', ') : [],
      technicalSpecs: initialProduct?.technicalSpecs || '',
      tagIcon: initialProduct?.tagIcon || '',
      tagText: initialProduct?.tagText || '',
      image: initialProduct?.image || '',
    },
    imageFile: null,
    isSubmitting: false,
    uploadProgress: 0,
    errors: {},
  })

  const isValid = computed(() => {
    return (
      state.form.name.trim() !== '' &&
      state.form.price !== '' &&
      parseFloat(state.form.price) > 0
    )
  })

  function resetForm() {
    state.form = {
      name: '',
      description: '',
      price: '',
      category: [],
      technicalSpecs: '',
      tagIcon: '',
      tagText: '',
      image: '',
    }
    state.imageFile = null
    state.uploadProgress = 0
    state.errors = {}
  }

  function setProduct(product: Partial<ProductFormData>) {
    state.form.name = product.name || ''
    state.form.description = product.description || ''
    state.form.price = product.price?.toString() || ''
    state.form.category = product.category ? product.category.split(', ') : []
    state.form.technicalSpecs = product.technicalSpecs || ''
    state.form.tagIcon = product.tagIcon || ''
    state.form.tagText = product.tagText || ''
    state.form.image = product.image || ''
    state.imageFile = null
  }

  function handleImageChange(event: Event) {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
      state.imageFile = target.files[0]
    }
  }

  function toggleCategory(category: string) {
    const index = state.form.category.indexOf(category)
    if (index === -1) {
      state.form.category.push(category)
    } else {
      state.form.category.splice(index, 1)
    }
  }

  function selectTag(icon: string, text: string) {
    state.form.tagIcon = icon
    state.form.tagText = text
  }

  function clearTag() {
    state.form.tagIcon = ''
    state.form.tagText = ''
  }

  function validateForm(): boolean {
    state.errors = {}

    if (!state.form.name.trim()) {
      state.errors.name = 'Produktname ist erforderlich'
    }

    if (!state.form.price || parseFloat(state.form.price) <= 0) {
      state.errors.price = 'Gültiger Preis ist erforderlich'
    }

    return Object.keys(state.errors).length === 0
  }

  async function uploadImage(): Promise<string | null> {
    if (!state.imageFile) {
      return state.form.image || null
    }

    const response = await api.upload<{ filename: string }>('/upload.php', state.imageFile)

    if (response.success && response.data) {
      return response.data.filename
    }

    throw new Error(response.message || 'Fehler beim Hochladen des Bildes')
  }

  async function createProduct(): Promise<{ success: boolean; message: string; productId?: number }> {
    if (!validateForm()) {
      return { success: false, message: Object.values(state.errors)[0] }
    }

    state.isSubmitting = true

    try {
      // Upload image first if there's one
      let imageName = state.form.image
      if (state.imageFile) {
        imageName = await uploadImage() || ''
      }

      const response = await api.post<{ id: number }>('/product.php', {
        name: state.form.name,
        description: state.form.description,
        price: parseFloat(state.form.price),
        image: imageName,
        category: state.form.category.join(', '),
        technical_specs: state.form.technicalSpecs,
        tag_icon: state.form.tagIcon,
        tag_text: state.form.tagText,
      })

      if (response.success) {
        return { 
          success: true, 
          message: response.message || 'Produkt erfolgreich erstellt',
          productId: response.data?.id
        }
      }

      return { success: false, message: response.message || 'Fehler beim Erstellen' }
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Fehler beim Erstellen des Produkts'
      return { success: false, message: errorMessage }
    } finally {
      state.isSubmitting = false
    }
  }

  async function updateProduct(productId: number): Promise<{ success: boolean; message: string }> {
    if (!validateForm()) {
      return { success: false, message: Object.values(state.errors)[0] }
    }

    state.isSubmitting = true

    try {
      // Upload image first if there's a new one
      let imageName = state.form.image
      if (state.imageFile) {
        imageName = await uploadImage() || state.form.image
      }

      const response = await api.put('/product.php', {
        id: productId,
        name: state.form.name,
        description: state.form.description,
        price: parseFloat(state.form.price),
        image: imageName,
        category: state.form.category.join(', '),
        technical_specs: state.form.technicalSpecs,
        tag_icon: state.form.tagIcon,
        tag_text: state.form.tagText,
      })

      if (response.success) {
        return { success: true, message: response.message || 'Produkt erfolgreich aktualisiert' }
      }

      return { success: false, message: response.message || 'Fehler beim Aktualisieren' }
    } catch (err: unknown) {
      const errorMessage = err instanceof Error ? err.message : 'Fehler beim Aktualisieren des Produkts'
      return { success: false, message: errorMessage }
    } finally {
      state.isSubmitting = false
    }
  }

  return {
    // State
    state,
    isValid,
    
    // Constants
    AVAILABLE_CATEGORIES,
    TAG_OPTIONS,
    
    // Methods
    resetForm,
    setProduct,
    handleImageChange,
    toggleCategory,
    selectTag,
    clearTag,
    validateForm,
    createProduct,
    updateProduct,
  }
}
