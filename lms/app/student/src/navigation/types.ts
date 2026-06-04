export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type AcademicsStackParamList = {
  AcademicsHub: undefined;
  Courses: undefined;
  Attendance: undefined;
  Transactions: undefined;
};

export type AssignmentsStackParamList = {
  AssignmentsHub: undefined;
  AssignmentsList: undefined;
  AssignmentDetail: { id: number };
  AssignmentFile: { id: number };
  TestResults: undefined;
  AllTestMarks: undefined;
  ClassTestResultDetail: { result: Record<string, unknown> };
};

export type AccountStackParamList = {
  AccountHome: undefined;
  Profile: undefined;
  ChangePassword: undefined;
};
