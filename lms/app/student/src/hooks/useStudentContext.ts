import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
} from 'react';
import { LmsApi } from '../api/lms';
import { AppStorage } from '../storage/AppStorage';
import {
  recordsForMonth,
  resolveCourseMeta,
  resolveStudentId,
  StoredStudentUser,
} from '../components/StudentHubUi';
import { TestResult } from '../components/MarksUi';

type LoadOptions = {
  showRefresh?: boolean;
};

type StudentContextValue = {
  loading: boolean;
  refreshing: boolean;
  refresh: (options?: LoadOptions) => Promise<void>;
  applyProfile: (profile: Record<string, unknown>) => Promise<void>;
  name: string;
  studentId: string | null;
  profile: Record<string, unknown> | null;
  dashboard: Record<string, unknown> | null;
  assignments: any[];
  marksResults: TestResult[];
  attendanceSummary: any[];
  monthRecords: any[];
  course: string | null;
  batch: string | null;
};

const StudentContext = createContext<StudentContextValue | null>(null);

async function syncStoredUserName(profile: Record<string, unknown> | null) {
  if (!profile?.name) return null;
  const user = await AppStorage.getUser<StoredStudentUser>();
  if (!user) return null;
  const updated = { ...user, name: String(profile.name) };
  await AppStorage.setUser(updated);
  return updated;
}

export function StudentContextProvider({ children }: { children: React.ReactNode }) {
  const hasDataRef = useRef(false);
  const inFlightRef = useRef(0);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [storedUser, setStoredUser] = useState<StoredStudentUser | null>(null);
  const [profile, setProfile] = useState<Record<string, unknown> | null>(null);
  const [dashboard, setDashboard] = useState<any>(null);
  const [assignments, setAssignments] = useState<any[]>([]);
  const [marksResults, setMarksResults] = useState<TestResult[]>([]);
  const [attendanceSummary, setAttendanceSummary] = useState<any[]>([]);
  const [monthRecords, setMonthRecords] = useState<any[]>([]);

  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);

  const load = useCallback(
    async (options?: LoadOptions) => {
      inFlightRef.current += 1;
      if (!hasDataRef.current) {
        setLoading(true);
      } else if (options?.showRefresh) {
        setRefreshing(true);
      }

      try {
        const user = await AppStorage.getUser<StoredStudentUser>();
        setStoredUser(user);

        const [dashResult, attendResult, assignResult, profileResult, marksResult] =
          await Promise.allSettled([
            LmsApi.dashboard(),
            LmsApi.attendance(month),
            LmsApi.assignments(),
            LmsApi.profile(),
            LmsApi.classTestResults(),
          ]);

        if (dashResult.status === 'fulfilled') {
          const data = dashResult.value.dashboard ?? dashResult.value;
          setDashboard(data);
          setAttendanceSummary(data?.attendance_summary ?? []);
        }

        if (attendResult.status === 'fulfilled') {
          setMonthRecords(recordsForMonth(attendResult.value.records ?? [], month));
        }

        if (assignResult.status === 'fulfilled') {
          setAssignments(assignResult.value.assignments ?? []);
        }

        if (marksResult.status === 'fulfilled') {
          setMarksResults(marksResult.value.results ?? []);
        }

        let nextProfile: Record<string, unknown> | null = null;
        if (profileResult.status === 'fulfilled') {
          nextProfile = profileResult.value.profile ?? null;
        } else if (dashResult.status === 'fulfilled') {
          const data = dashResult.value.dashboard ?? dashResult.value;
          nextProfile = data?.profile ?? null;
        }
        if (nextProfile) {
          setProfile(nextProfile);
        }

        const syncedUser = await syncStoredUserName(nextProfile);
        if (syncedUser) {
          setStoredUser(syncedUser);
        }

        hasDataRef.current = true;
      } finally {
        inFlightRef.current = Math.max(0, inFlightRef.current - 1);
        if (inFlightRef.current === 0) {
          setLoading(false);
          setRefreshing(false);
        }
      }
    },
    [month],
  );

  const applyProfile = useCallback(async (next: Record<string, unknown>) => {
    setProfile(next);
    setDashboard((current: any) =>
      current ? { ...current, profile: { ...(current.profile ?? {}), ...next } } : current,
    );
    const syncedUser = await syncStoredUserName(next);
    if (syncedUser) {
      setStoredUser(syncedUser);
    }
    hasDataRef.current = true;
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const name = String(profile?.name ?? storedUser?.name ?? 'Student');
  const studentId = resolveStudentId(profile, storedUser);
  const courseMeta = resolveCourseMeta({ profile, attendanceSummary, monthRecords });

  const value = useMemo<StudentContextValue>(
    () => ({
      loading,
      refreshing,
      refresh: load,
      applyProfile,
      name,
      studentId,
      profile,
      dashboard,
      assignments,
      marksResults,
      attendanceSummary,
      monthRecords,
      course: courseMeta.course,
      batch: courseMeta.batch,
    }),
    [
      loading,
      refreshing,
      load,
      applyProfile,
      name,
      studentId,
      profile,
      dashboard,
      assignments,
      marksResults,
      attendanceSummary,
      monthRecords,
      courseMeta.course,
      courseMeta.batch,
    ],
  );

  return React.createElement(StudentContext.Provider, { value }, children);
}

export function useStudentContext(): StudentContextValue {
  const ctx = useContext(StudentContext);
  if (!ctx) {
    throw new Error('useStudentContext must be used within StudentContextProvider');
  }
  return ctx;
}
