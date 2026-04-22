import api from '@/lib/axios';
import type { ApiList, College } from '@/types';

export const getColleges = async (): Promise<College[]> => {
  const { data } = await api.get<ApiList<College>>('/colleges');
  return data.data;
};
