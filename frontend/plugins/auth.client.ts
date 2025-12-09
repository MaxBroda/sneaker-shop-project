export default defineNuxtPlugin(() => {
  const { user, token } = useAuth();

  if (typeof window !== 'undefined') {
    const savedToken = localStorage.getItem('token');
    const savedUser = localStorage.getItem('user');

    if (savedToken && savedUser) {
      try {
        token.value = savedToken;
        user.value = JSON.parse(savedUser);
        console.log('Auth state restored from localStorage');
      } catch (error) {
        console.error('Failed to restore auth state:', error);
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    }
  }
});
