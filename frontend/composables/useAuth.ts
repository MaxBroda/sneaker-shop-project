interface Address {
  street: string;
  house_number: string;
  city: string;
  postal_code: string;
  country: string;
}

interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  role: "customer" | "seller";
  address?: Address;
}

interface ApiResponse<T = unknown> {
  success: boolean;
  message?: string;
  user?: User;
  token?: string;
  data?: T;
}

export const useAuth = () => {
  const user = useState<User | null>("user", () => null);
  const token = useState<string | null>("token", () => null);

  const API_URL = "http://localhost:8080/api";

  onMounted(() => {
    const savedToken = localStorage.getItem("token");
    const savedUser = localStorage.getItem("user");
    if (savedToken && savedUser) {
      token.value = savedToken;
      user.value = JSON.parse(savedUser);
      console.log("Session von localStorage wiederhergestellt:", user.value);
    }
  });

  async function login(email: string, password: string) {
    try {
      console.log("Versuche Anmeldung:", email);

      const res = await $fetch<ApiResponse>(`${API_URL}/login.php`, {
        method: "POST",
        body: { email, password },
      });

      console.log("Login Antwort:", res);

      if (res.success && res.user && res.token) {
        user.value = res.user;
        token.value = res.token;
        localStorage.setItem("token", res.token);
        localStorage.setItem("user", JSON.stringify(res.user));
        console.log("Anmeldung erfolgreich:", res.user);
        return true;
      }

      console.warn("Anmeldung fehlgeschlagen:", res.message);
      return false;
    } catch (err) {
      console.error("Anmeldefehler:", err);
      return false;
    }
  }

  async function register(
    email: string,
    firstName: string,
    lastName: string,
    password: string,
    role: string,
    address?: Record<string, string>
  ): Promise<ApiResponse> {
    try {
      const res = await $fetch<ApiResponse>(`${API_URL}/register.php`, {
        method: "POST",
        body: { email, firstName, lastName, password, role, address },
      });

      console.log("Registrierung Antwort:", res);
      if (res.success && res.user && res.token) {
        loginUserDirect(res.user, res.token);
      }

      return res;
    } catch (err) {
      console.error("Registrierungsfehler:", err);

      if (err && typeof err === "object" && "data" in err) {
        const error = err as { data?: { message?: string } };
        if (error.data?.message) {
          return { success: false, message: error.data.message };
        }
      }

      return { success: false, message: "Netzwerk- oder Serverfehler" };
    }
  }

  async function logout() {
    try {
      if (!token.value) {
        console.warn("Kein Token für Logout gefunden");
        return;
      }

      console.log("Abmeldung...");

      await $fetch(`${API_URL}/logout.php`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });

      user.value = null;
      token.value = null;
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      console.log("Erfolgreich abgemeldet");
    } catch (err) {
      console.error("Abmeldefehler:", err);
    }
  }

  function loginUserDirect(newUser: User, newToken: string) {
    user.value = newUser;
    token.value = newToken;
    localStorage.setItem("token", newToken);
    localStorage.setItem("user", JSON.stringify(newUser));
  }

  return { user, token, login, register, logout, loginUserDirect };
};
