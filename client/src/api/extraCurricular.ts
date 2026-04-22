import api from "@/lib/axios";
import type {
  ApiItem,
  CreateExtraCurricularPayload,
  ExtraCurricular,
} from "@/types";

export const createExtraCurricular = async (
  payload: CreateExtraCurricularPayload,
): Promise<ExtraCurricular> => {
  console.log(payload);

  const { data } = await api.post<ApiItem<ExtraCurricular>>(
    "/extra-curricular",
    payload,
  );
  return data.data;
};
