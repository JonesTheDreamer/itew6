import api from "@/lib/axios";
import type {
  ApiItem,
  // ApiList,
  Award,
  CreateAwardPayload,
  // Faculty,
  // FacultyFilters,
} from "@/types";

// export const getFaculty = async (
//   filters?: FacultyFilters,
// ): Promise<Faculty[]> => {
//   const { data } = await api.get<ApiList<Faculty>>("/faculty", {
//     params: filters,
//   });
//   return data.data;
// };

// export const getFacultyMember = async (id: number): Promise<Faculty> => {
//   const { data } = await api.get<ApiItem<Faculty>>(`/faculty/${id}`);
//   return data.data;
// };

export const createAward = async (
  payload: CreateAwardPayload,
): Promise<Award> => {
  const { data } = await api.post<ApiItem<Award>>("/awards", payload);
  return data.data;
};
