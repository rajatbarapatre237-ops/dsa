export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type WorkStackParamList = {
  WorkHub: undefined;
  AssignmentsList: { contentKind?: 'assignment' } | undefined;
  NotesList: { contentKind: 'note' };
  AddAssignment: { contentKind?: 'assignment' } | undefined;
  AddNote: { contentKind: 'note' };
  AssignmentDetail: { id: number };
  AssignmentFile: { id: number };
  ClassTests: undefined;
  CreateClassTest: undefined;
  EnterMarks:
    | {
        courseId?: string;
        subjectId?: string;
        testId?: string;
      }
    | undefined;
  TestResults: undefined;
  ClassTestResultDetail: { result: Record<string, unknown> };
};

export type StudentsStackParamList = {
  StudentsList: undefined;
  StudentDetail: { id: string };
};

export type AttendanceStackParamList = {
  AttendanceHub: undefined;
  ViewAttendance: undefined;
  StudentAttendanceList: undefined;
  StudentAttendanceSummary: { id: string; name?: string };
  MyAttendance: undefined;
  AddAttendance: undefined;
};
