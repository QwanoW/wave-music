import axios, { AxiosError } from 'axios';
import { useToast } from '@/components/ui/toast/use-toast';

import { getToken, removeToken } from '@/lib/token';
import { userStore } from '@/stores/user';
import { API_URL } from '@/constants';

const api = axios.create({
  baseURL: API_URL,
});

api.interceptors.request.use(
  (config) => {
    const token = getToken();

    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

api.interceptors.response.use(
  (response) => {
    if (response.status === 401) {
      removeToken();
      userStore.reset();
      if (!window.location.href.includes('/auth/login')) {
        window.location.href = '/auth/login';
      }
    }

    return response;
  },
  (error) => {
    if (error.response.status === 401 && error.response.config.url !== '/auth/login.php') {
      const { toast } = useToast();

      removeToken();
      userStore.reset();
      toast({
        title: 'Произошла ошибка',
        description: error.response.data.message,
        variant: 'destructive',
      });

      if (!window.location.href.includes('/auth/login')) {
        window.location.href = '/auth/login';
      }
    }

    if (!error.response.data.message && error instanceof AxiosError && error.response) {
      error.response.data.message = error.message;
    }

    return Promise.reject(error);
  },
);

export default api;
