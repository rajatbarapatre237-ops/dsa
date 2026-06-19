import React, { useCallback, useMemo, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionTitle, StudentContextCard } from '../components/DashboardUi';
import {
  ExploreMarksTiles,
  LatestMarksCard,
  MarksOverviewCard,
  RecentMarksCard,
  SubjectGrowthCards,
  TestResult,
  groupResultsBySubject,
  marksStats,
} from '../components/MarksUi';
import { LmsApi } from '../api/lms';
import { MarksStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

function sortByDateDesc(results: TestResult[]) {
  return [...results].sort((a, b) => String(b.test_date ?? '').localeCompare(String(a.test_date ?? '')));
}

export default function MarksHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<MarksStackParamList>>();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [dashboard, setDashboard] = useState<any>(null);
  const [results, setResults] = useState<TestResult[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const [dashRes, marksRes]: any[] = await Promise.all([
          LmsApi.dashboard(),
          LmsApi.classTestResults(),
        ]);
        setDashboard(dashRes.dashboard ?? dashRes);
        setResults(marksRes.results ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  const child = dashboard?.child;
  const sorted = useMemo(() => sortByDateDesc(results), [results]);
  const latest = sorted[0];
  const stats = marksStats(sorted);
  const recent = sorted.slice(0, 5);
  const subjectGrowth = useMemo(() => groupResultsBySubject(sorted), [sorted]);

  const openDetail = (result: TestResult) => {
    navigation.navigate('ClassTestResultDetail', { result: result as Record<string, unknown> });
  };

  const openSubjectResults = (subjectName: string) => {
    navigation.navigate('TestResults', { subjectName });
  };

  return (
    <ScreenLayout
      title="Growth"
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <StudentContextCard name={child?.name} course={child?.course_name} batch={child?.batch} />

      <MarksOverviewCard
        total={stats.total}
        passed={stats.passed}
        failed={stats.failed}
        averagePercent={stats.averagePercent}
        passRate={stats.passRate}
      />

      <LatestMarksCard
        result={latest}
        onPress={latest ? () => openDetail(latest) : undefined}
      />

      <SectionTitle>Growth by subject</SectionTitle>
      <SubjectGrowthCards subjects={subjectGrowth} onSubjectPress={openSubjectResults} />

      <SectionTitle>Explore</SectionTitle>
      <ExploreMarksTiles
        onAllMarks={() => navigation.navigate('AllTestMarks')}
        onClassResults={() => navigation.navigate('TestResults')}
        onSubjectResults={() => navigation.navigate('TestResults')}
      />

      <RecentMarksCard
        results={recent}
        onViewAll={() => navigation.navigate('AllTestMarks')}
        onItemPress={openDetail}
      />
    </ScreenLayout>
  );
}
