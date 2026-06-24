import { APP_ROLE } from '../config';
import { http } from './http';
import type { ApiError } from './http';
import { fileToBase64, normalizeUploadFile, type PickedFile } from '../utils/mediaPicker';

const p = `/${APP_ROLE}`;
const UPLOAD_TIMEOUT_MS = 180000;
const MAX_UPLOAD_BYTES = 20 * 1024 * 1024;

type CtStudentsMarksParams = {
  test_id: number;
  session_name?: string;
  batch?: string;
};

async function fetchCtStudentsMarksLegacy(params: CtStudentsMarksParams) {
  const testRes = await http.get(`${p}/form/class-test/${params.test_id}`);
  const test = testRes.data?.test;
  if (!test) {
    throw { status: 404, message: 'Test not found' } satisfies ApiError;
  }

  const courseName = String(test.course_name ?? '').trim();
  const session = String(params.session_name ?? '').trim();
  const batch = String(params.batch ?? '').trim();
  let students: { id: number | string; name: string }[] = [];

  if (courseName && session) {
    const res = await http.get(`${p}/form/students`, {
      params: {
        course: courseName,
        session,
        ...(batch ? { batch } : {}),
      },
    });
    students = res.data?.students ?? [];
  } else if (courseName) {
    const all: { id: number | string; name: string }[] = [];
    let page = 1;
    let totalPages = 1;
    do {
      const res = await http.get(`${p}/students`, {
        params: {
          course: courseName,
          ...(batch ? { batch } : {}),
          page,
          per_page: 50,
        },
      });
      all.push(...(res.data?.students ?? []));
      totalPages = res.data?.pagination?.total_pages ?? 1;
      page += 1;
    } while (page <= totalPages);
    students = all;
  }

  const marksRes = await http.get(`${p}/test-results`, { params: { test_id: params.test_id } });
  const marksByStudent: Record<string, number | null> = {};
  for (const row of marksRes.data?.results ?? []) {
    const sid = String(row.student_id ?? '');
    if (sid) {
      marksByStudent[sid] = row.marks_obtained != null ? Number(row.marks_obtained) : null;
    }
  }

  return {
    status: 'success',
    test: {
      id: test.id,
      test_name: test.test_name,
      test_date: test.test_date,
      total_marks: test.total_marks,
      passing_marks: test.passing_marks,
      course_name: test.course_name,
      subject_name: test.subject_name,
    },
    students: students.map(s => ({
      student_id: Number(s.id),
      name: s.name,
      marks_obtained: marksByStudent[String(s.id)] ?? null,
    })),
  };
}

async function postAssignmentFileBase64(
  file: PickedFile,
  meta: {
    content_kind?: 'assignment' | 'note';
    batch_name: string;
    document_name: string;
    subject_id?: number;
    subject_name?: string;
  },
) {
  const upload = normalizeUploadFile(file);
  const fileBase64 = await fileToBase64(upload);
  const estimatedBytes = Math.floor((fileBase64.length * 3) / 4);
  if (estimatedBytes > MAX_UPLOAD_BYTES) {
    throw {
      status: 422,
      message: 'File is too large (max 20 MB).',
    } satisfies ApiError;
  }

  return http
    .post(
      `${p}/assignments`,
      {
        type: 'file',
        content_kind: meta.content_kind ?? 'assignment',
        batch_name: meta.batch_name,
        document_name: meta.document_name,
        subject_id: meta.subject_id,
        subject_name: meta.subject_name,
        file_base64: fileBase64,
        file_name: upload.name,
        file_mime: upload.type,
      },
      { timeout: UPLOAD_TIMEOUT_MS },
    )
    .then(
      r =>
        r.data as {
          assignment_id?: number;
          file_count?: number;
          message?: string;
        },
    );
}

async function patchAssignmentFileBase64(
  id: number,
  file: PickedFile,
  meta: {
    batch_name: string;
    document_name: string;
    subject_id?: number;
    subject_name?: string;
  },
) {
  const upload = normalizeUploadFile(file);
  const fileBase64 = await fileToBase64(upload);
  const estimatedBytes = Math.floor((fileBase64.length * 3) / 4);
  if (estimatedBytes > MAX_UPLOAD_BYTES) {
    throw {
      status: 422,
      message: 'File is too large (max 20 MB).',
    } satisfies ApiError;
  }

  return http
    .patch(
      `${p}/assignments/${id}`,
      {
        batch_name: meta.batch_name,
        document_name: meta.document_name,
        subject_id: meta.subject_id,
        subject_name: meta.subject_name,
        file_base64: fileBase64,
        file_name: upload.name,
        file_mime: upload.type,
      },
      { timeout: UPLOAD_TIMEOUT_MS },
    )
    .then(r => r.data);
}

async function appendAssignmentFileBase64(id: number, file: PickedFile) {
  const upload = normalizeUploadFile(file);
  const fileBase64 = await fileToBase64(upload);
  const estimatedBytes = Math.floor((fileBase64.length * 3) / 4);
  if (estimatedBytes > MAX_UPLOAD_BYTES) {
    throw {
      status: 422,
      message: `${upload.name} is too large (max 20 MB).`,
    } satisfies ApiError;
  }

  const payload = {
    file_base64: fileBase64,
    file_name: upload.name,
    file_mime: upload.type,
  };
  const opts = { timeout: UPLOAD_TIMEOUT_MS };
  const attempts = [
    () =>
      http.post(`${p}/assignments`, { append_to_id: id, ...payload }, opts),
    () => http.post(`${p}/assignments/${id}/files`, payload, opts),
    () => http.patch(`${p}/assignments/${id}`, { append_file: true, ...payload }, opts),
  ];

  let lastError: ApiError | undefined;
  for (const attempt of attempts) {
    try {
      const response = await attempt();
      return response.data as { assignment_id?: number; file_count?: number };
    } catch (error) {
      lastError = error as ApiError;
      if (lastError.status !== 404 && lastError.status !== 405) {
        throw lastError;
      }
    }
  }

  throw lastError ?? { status: 404, message: 'Could not append file to assignment.' };
}

export const LmsApi = {
  dashboard: () => http.get(`${p}/dashboard`).then(r => r.data),
  profile: () => http.get(`${p}/profile`).then(r => r.data),
  students: (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    course?: string;
    batch?: string;
  }) => http.get(`${p}/students`, { params }).then(r => r.data),
  courses: () => http.get(`${p}/courses`).then(r => r.data),
  attendance: (month?: string) =>
    http
      .get(`${p}/attendance`, { params: { month: month ?? new Date().toISOString().slice(0, 7) } })
      .then(r => r.data),
  myAttendance: () => http.get(`${p}/my-attendance`).then(r => r.data),
  assignments: (params?: { content_kind?: 'assignment' | 'note' }) =>
    http.get(`${p}/assignments`, { params }).then(r => r.data),
  assignment: (id: number) => http.get(`${p}/assignments/${id}`).then(r => r.data),
  student: (id: string) => http.get(`${p}/students/${id}`).then(r => r.data),
  studentAttendanceSummary: (id: string) =>
    http.get(`${p}/students/${id}/attendance-summary`).then(r => r.data),
  allBatches: () => http.get(`${p}/form/all-batches`).then(r => r.data),
  subjectsForBatch: (batch_name: string) =>
    http.get(`${p}/form/subjects`, { params: { batch_name } }).then(r => r.data),
  createAssignmentLink: (body: {
    type: 'link';
    content_kind?: 'assignment' | 'note';
    batch_name: string;
    document_name: string;
    subject_id?: number;
    subject_name?: string;
    link: string;
  }) => http.post(`${p}/assignments`, body).then(r => r.data),
  createAssignmentFile: (body: {
    type: 'file';
    content_kind?: 'assignment' | 'note';
    batch_name: string;
    document_name: string;
    subject_id?: number;
    subject_name?: string;
    file_base64: string;
    file_name: string;
    file_mime?: string;
  }) =>
    http.post(`${p}/assignments`, body, { timeout: 120000 }).then(r => r.data),
  createAssignmentFileUpload: (
    file: PickedFile,
    meta: {
      content_kind?: 'assignment' | 'note';
      batch_name: string;
      document_name: string;
      subject_id?: number;
      subject_name?: string;
    },
  ) => postAssignmentFileBase64(file, meta),
  createAssignmentFilesUpload: async (
    files: PickedFile[],
    meta: {
      content_kind?: 'assignment' | 'note';
      batch_name: string;
      document_name: string;
      subject_id?: number;
      subject_name?: string;
    },
    onProgress?: (current: number, total: number) => void,
  ) => {
    const label = meta.content_kind === 'note' ? 'Note' : 'Assignment';
    let assignmentId: number | undefined;

    try {
      for (let index = 0; index < files.length; index += 1) {
        onProgress?.(index + 1, files.length);
        if (index === 0) {
          const created = await postAssignmentFileBase64(files[index], meta);
          assignmentId = Number(created.assignment_id);
          if (!assignmentId) {
            throw {
              status: 500,
              message: 'Upload started but the server did not return an assignment id.',
            } satisfies ApiError;
          }
        } else if (assignmentId) {
          await appendAssignmentFileBase64(assignmentId, files[index]);
        }
      }
    } catch (error) {
      if (assignmentId) {
        await http.delete(`${p}/assignments/${assignmentId}`).catch(() => undefined);
      }
      throw error;
    }

    return {
      status: 'success',
      message:
        files.length > 1
          ? `${label} added with ${files.length} files`
          : `${label} added`,
      count: 1,
      file_count: files.length,
    };
  },
  updateAssignmentFileUpload: (
    id: number,
    file: PickedFile,
    meta: {
      batch_name: string;
      document_name: string;
      subject_id?: number;
      subject_name?: string;
    },
  ) => patchAssignmentFileBase64(id, file, meta),
  updateAssignment: (
    id: number,
    body: {
      batch_name?: string;
      document_name?: string;
      subject_id?: number;
      subject_name?: string;
      link?: string;
      file_base64?: string;
      file_name?: string;
      file_mime?: string;
    },
  ) => http.patch(`${p}/assignments/${id}`, body, { timeout: 120000 }).then(r => r.data),
  updateAssignmentStatus: (id: number, status: boolean) =>
    http.patch(`${p}/assignments/${id}/status`, { status }).then(r => r.data),
  deleteAssignment: (id: number) => http.delete(`${p}/assignments/${id}`).then(r => r.data),
  transactions: () => http.get(`${p}/transactions`).then(r => r.data),
  salary: () => http.get(`${p}/salary`).then(r => r.data),
  classTests: () => http.get(`${p}/class-tests`).then(r => r.data),
  classTestResults: () => http.get(`${p}/class-test-results`).then(r => r.data),
  allTestMarks: () => http.get(`${p}/all-test-marks`).then(r => r.data),
  testResults: () => http.get(`${p}/test-results`).then(r => r.data),
  changePassword: (body: { current_password: string; new_password: string }) =>
    http.post(`${p}/change-password`, body).then(r => r.data),

  formSessions: () => http.get(`${p}/form/sessions`).then(r => r.data),
  formCourses: () => http.get(`${p}/form/courses`).then(r => r.data),
  formBatches: (course: string) =>
    http.get(`${p}/form/batches`, { params: { course } }).then(r => r.data),
  formStudents: (params: { course: string; session: string; batch?: string }) =>
    http.get(`${p}/form/students`, { params }).then(r => r.data),
  saveAttendance: (body: {
    date: string;
    course: string;
    batch: string;
    records: { student_id: string; status: 'present' | 'absent' }[];
  }) => http.post(`${p}/form/attendance`, body).then(r => r.data),

  ctCourses: (session_name?: string) =>
    http.get(`${p}/form/class-test/courses`, { params: session_name ? { session_name } : {} }).then(r => r.data),
  ctSubjects: (course_id: number) =>
    http.get(`${p}/form/class-test/subjects`, { params: { course_id } }).then(r => r.data),
  ctTests: (course_id: number, subject_id: number) =>
    http.get(`${p}/form/class-test/tests`, { params: { course_id, subject_id } }).then(r => r.data),
  createClassTest: (body: Record<string, unknown>) =>
    http.post(`${p}/form/class-test`, body).then(r => r.data),
  classTest: (id: number) => http.get(`${p}/form/class-test/${id}`).then(r => r.data),
  updateClassTest: (id: number, body: Record<string, unknown>) =>
    http.patch(`${p}/form/class-test/${id}`, body).then(r => r.data),
  ctStudentsMarks: async (params: CtStudentsMarksParams) => {
    const urls = [`${p}/form/class-test-students`, `${p}/form/class-test/students`];
    for (const url of urls) {
      try {
        return (await http.get(url, { params })).data;
      } catch (error) {
        const apiError = error as ApiError;
        if (apiError?.status === 401 || apiError?.status === 403 || apiError?.status === 422) {
          throw error;
        }
      }
    }

    return fetchCtStudentsMarksLegacy(params);
  },
  saveClassTestMarks: (body: { test_id: number; marks: Record<string, string> }) =>
    http.post(`${p}/form/class-test/marks`, body).then(r => r.data),
};
