import { reactive } from 'vue';

import { hasToken, removeToken, setToken } from '@/lib/token';
import api from '@/lib/axios';
import { AxiosError } from 'axios';
import { UserRole } from '@/constants/index';

interface User {
  id: number;
  username: string;
  email: string;
  role: UserRole;
  isArtist: boolean;
  created_at: Date;
}

interface UserStore {
  isAuthenticated: boolean;
  user?: User;

  authorize(token: string): void;

  register(
    username: string,
    email: string,
    password: string,
  ): Promise<{ success?: string; error?: string }>;

  login(email: string, password: string): Promise<{ success?: string; error?: string }>;

  reset(): void;

  logout(): void;
}

export const userStore = reactive<UserStore>({
  isAuthenticated: hasToken(),
  user: undefined,

  reset() {
    this.isAuthenticated = false;
    this.user = undefined;
  },

  authorize(token: string) {
    setToken(token);
    this.isAuthenticated = true;
  },

  async login(email: string, password: string) {
    try {
      const response = await api.post('/auth/login.php', {
        email,
        password,
      });

      const { message, jwt } = response.data;

      this.authorize(jwt);

      return { success: message };
    } catch (error) {
      console.log(error);

      if (error instanceof AxiosError && error.response?.data.message) {
        return { error: error.response.data.message };
      }

      return { error: 'Произошла ошибка, попробуйте позже' };
    }
  },

  async register(username: string, email: string, password: string) {
    try {
      const response = await api.post('/auth/register.php', {
        username,
        email,
        password,
      });

      return { success: response.data.message };
    } catch (error) {
      console.log(error);

      if (error instanceof AxiosError && error.response?.data.message) {
        return { error: error.response.data.message };
      }

      return { error: 'Произошла ошибка, попробуйте позже' };
    }
  },

  logout() {
    this.reset();
    removeToken();

    window.location.href = '/auth/login';
  },
});

export const fetchUserData = async () => {
  try {
    const response = await api.post('/user/validate_token.php');

    if (response.status !== 200) {
      userStore.reset();
      return;
    }

    userStore.isAuthenticated = true;
    userStore.user = response.data.data;
  } catch (error) {}
};
