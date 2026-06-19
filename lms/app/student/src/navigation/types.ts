export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type AcademicsStackParamList = {
  AcademicsHub: undefined;
  Courses: undefined;
  Attendance: undefined;
  Transactions: undefined;
  TestResults: undefined;
  AllTestMarks: undefined;
  ClassTestResultDetail: { result: Record<string, unknown> };
};

export type AssignmentsStackParamList = {
  AssignmentsHub: undefined;
  ContentSubjects: { contentKind: 'assignment' | 'note' };
  ContentList: {
    contentKind: 'assignment' | 'note';
    subjectId?: number | null;
    subjectName: string;
  };
  AssignmentsList: undefined;
  AssignmentDetail: { id: number };
  AssignmentFile: { id: number };
};

export type AccountStackParamList = {
  AccountHome: undefined;
  Profile: undefined;
  ChangePassword: undefined;
};
