import axios, { AxiosError } from 'axios';
import { useRouter } from 'vue-router/auto';
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
      window.location.href = '/auth/login';
    }

    return response;
  },
  (error) => {
    if (error.response.status === 401) {
      const router = useRouter();
      const { toast } = useToast();

      removeToken();
      userStore.reset();
      toast({
        title: 'Произошла ошибка',
        description: error.response.data.message,
        variant: 'destructive',
      });
      router.push('/auth/login');
    }

    if (error instanceof AxiosError && error.response) {
      error.response.data.message = error.message;
    }

    return Promise.reject(error);
  },
);

export default api;
