import React from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionHeader, SectionTitle, StudentContextCard } from '../components/DashboardUi';
import { AssignmentsSummaryCard } from '../components/AssignmentUi';
import { ExploreAssignmentsTiles, RecentAssignmentsCard } from '../components/StudentHubUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';
import { AssignmentsStackParamList } from '../navigation/types';

export default function AssignmentsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const ctx = useStudentContext();
  useRefreshStudentOnFocus();

  const recentAssignments = ctx.assignments.slice(0, 5);

  return (
    <ScreenLayout
      title="Assignments"
      refreshing={ctx.refreshing}
      onRefresh={() => ctx.refresh({ showRefresh: true })}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <AssignmentsSummaryCard count={ctx.assignments.length} />

      <SectionTitle>Explore</SectionTitle>
      <ExploreAssignmentsTiles onAssignments={() => navigation.navigate('AssignmentsList')} />

      <SectionHeader
        title="Recent assignments"
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
    </ScreenLayout>
  );
}
