import api from '@/lib/axios';
import type { ApiItem, ApiList, Organization, OrgMember } from '@/types';

export const getOrganizations = async (): Promise<Organization[]> => {
  const { data } = await api.get<ApiList<Organization>>('/organizations');
  return data.data;
};

export const getOrganization = async (id: number): Promise<Organization> => {
  const { data } = await api.get<ApiItem<Organization>>(`/organizations/${id}`);
  return data.data;
};

export const createOrganization = async (payload: {
  organizationName: string;
  organizationDescription?: string | null;
  dateCreated?: string | null;
  collegeId?: number | null;
  isActive?: boolean;
}): Promise<Organization> => {
  const { data } = await api.post<ApiItem<Organization>>('/organizations', payload);
  return data.data;
};

type UpdateOrganizationPayload = {
  organizationName?: string;
  organizationDescription?: string | null;
  dateCreated?: string | null;
  collegeId?: number | null;
  isActive?: boolean;
};

export const updateOrganization = async (
  id: number,
  payload: UpdateOrganizationPayload,
): Promise<Organization> => {
  const { data } = await api.put<ApiItem<Organization>>(`/organizations/${id}`, payload);
  return data.data;
};

export const deleteOrganization = async (id: number): Promise<void> => {
  await api.delete(`/organizations/${id}`);
};

export const getOrgMembers = async (organizationId: number): Promise<OrgMember[]> => {
  const { data } = await api.get<ApiList<OrgMember>>('/user-organizations', {
    params: { organizationId },
  });
  return data.data;
};

export const addOrgMember = async (payload: {
  userId: number;
  organizationId: number;
  role?: string | null;
  dateJoined?: string | null;
}): Promise<OrgMember> => {
  const { data } = await api.post<ApiItem<OrgMember>>('/user-organizations', payload);
  return data.data;
};

export const removeOrgMember = async (id: number): Promise<void> => {
  const today = new Date().toISOString().split('T')[0];
  await api.put(`/user-organizations/${id}`, { dateLeft: today });
};
