import api from '@/lib/axios';
import type { ApiItem, ApiList, Faculty, FacultyFilters } from '@/types';

export const getFaculty = async (filters?: FacultyFilters): Promise<Faculty[]> => {
  const { data } = await api.get<ApiList<Faculty>>('/faculty', { params: filters });
  return data.data;
};

export const getFacultyMember = async (id: number): Promise<Faculty> => {
  const { data } = await api.get<ApiItem<Faculty>>(`/faculty/${id}`);
  return data.data;
};

export const createFaculty = async (payload: Partial<Faculty>): Promise<Faculty> => {
  const { data } = await api.post<ApiItem<Faculty>>('/faculty', payload);
  return data.data;
};

export const updateFaculty = async (id: number, payload: Partial<Faculty>): Promise<Faculty> => {
  const { data } = await api.put<ApiItem<Faculty>>(`/faculty/${id}`, payload);
  return data.data;
};

export const deleteFaculty = async (id: number): Promise<void> => {
  await api.delete(`/faculty/${id}`);
};
