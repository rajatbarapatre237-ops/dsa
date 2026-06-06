import React, { useCallback, useMemo, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionHeader, SectionTitle, StudentContextCard } from '../components/DashboardUi';
import {
  LatestMarksCard,
  MarksOverviewCard,
  RecentMarksCard,
  TestResult,
  marksStats,
} from '../components/MarksUi';
import { ExploreAssignmentsTiles, RecentAssignmentsCard } from '../components/StudentHubUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';
import { LmsApi } from '../api/lms';
import { AssignmentsStackParamList } from '../navigation/types';

function sortByDateDesc(results: TestResult[]) {
  return [...results].sort((a, b) => String(b.test_date ?? '').localeCompare(String(a.test_date ?? '')));
}

export default function AssignmentsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const ctx = useStudentContext();
  useRefreshStudentOnFocus();
  const [marksLoading, setMarksLoading] = useState(true);
  const [results, setResults] = useState<TestResult[]>([]);

  const loadMarks = useCallback(async () => {
    setMarksLoading(true);
    try {
      const marksResult = await Promise.allSettled([LmsApi.classTestResults()]);
      if (marksResult[0].status === 'fulfilled') {
        setResults(marksResult[0].value.results ?? []);
      }
    } finally {
      setMarksLoading(false);
    }
  }, []);

  React.useEffect(() => {
    loadMarks();
  }, [loadMarks]);

  const refresh = useCallback(async () => {
    await Promise.all([ctx.refresh(), loadMarks()]);
  }, [ctx, loadMarks]);

  const sortedMarks = useMemo(() => sortByDateDesc(results), [results]);
  const latest = sortedMarks[0];
  const stats = marksStats(sortedMarks);
  const recentMarks = sortedMarks.slice(0, 5);
  const recentAssignments = ctx.assignments.slice(0, 5);

  const openDetail = (result: TestResult) => {
    navigation.navigate('ClassTestResultDetail', { result: result as Record<string, unknown> });
  };

  return (
    <ScreenLayout title="Assignments" refreshing={ctx.loading || marksLoading} onRefresh={refresh}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <SectionTitle>Latest result</SectionTitle>
      <LatestMarksCard result={latest} onPress={latest ? () => openDetail(latest) : undefined} />

      <SectionTitle>Performance</SectionTitle>
      <MarksOverviewCard
        total={stats.total}
        passed={stats.passed}
        failed={stats.failed}
        averagePercent={stats.averagePercent}
        passRate={stats.passRate}
      />

      <SectionTitle>Explore</SectionTitle>
      <ExploreAssignmentsTiles
        onAssignments={() => navigation.navigate('AssignmentsList')}
        onTestResults={() => navigation.navigate('TestResults')}
        onAllMarks={() => navigation.navigate('AllTestMarks')}
      />

      <SectionHeader
        title="Assignments"
        actionLabel="See all"
        onAction={() => navigation.navigate('AssignmentsList')}
      />
      <RecentAssignmentsCard
        assignments={recentAssignments}
        onItemPress={item => {
          if (item?.id != null) {
            navigation.navigate('AssignmentDetail', { id: Number(item.id) });
          } else {
            navigation.navigate('AssignmentsList');
          }
        }}
      />

      <SectionHeader
        title="Recent tests"
        actionLabel="See all"
        onAction={() => navigation.navigate('AllTestMarks')}
      />
      <RecentMarksCard results={recentMarks} onItemPress={openDetail} showHeader={false} />
    </ScreenLayout>
  );
}
