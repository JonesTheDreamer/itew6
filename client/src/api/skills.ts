import api from '@/lib/axios';
import type { ApiItem, ApiList, Skill } from '@/types';

export const getSkills = async (): Promise<Skill[]> => {
  const { data } = await api.get<ApiList<Skill>>('/skills');
  return data.data;
};

export const createSkill = async (payload: { name: string; isAcademic: boolean }): Promise<Skill> => {
  const { data } = await api.post<ApiItem<Skill>>('/skills', payload);
  return data.data;
};

export const updateSkill = async (id: number, payload: { name: string; isAcademic: boolean }): Promise<Skill> => {
  const { data } = await api.put<ApiItem<Skill>>(`/skills/${id}`, payload);
  return data.data;
};

export const deleteSkill = async (id: number): Promise<void> => {
  await api.delete(`/skills/${id}`);
};
