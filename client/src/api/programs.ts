import api from '@/lib/axios';
import type { ApiList, Program } from '@/types';

export const getPrograms = async (): Promise<Program[]> => {
  const { data } = await api.get<ApiList<Program>>('/programs');
  return data.data;
};
