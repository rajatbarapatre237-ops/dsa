import React, { useMemo } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionHeader, SectionTitle, StudentContextCard } from '../components/DashboardUi';
import { WorkSummaryCardsRow } from '../components/AssignmentUi';
import { ExploreAssignmentsTiles, RecentAssignmentsCard } from '../components/StudentHubUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';
import { AssignmentsStackParamList } from '../navigation/types';

function contentKindOf(item: any) {
  return item.content_kind === 'note' ? 'note' : 'assignment';
}

export default function AssignmentsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const ctx = useStudentContext();
  useRefreshStudentOnFocus();

  const assignments = useMemo(
    () => ctx.assignments.filter((item: any) => contentKindOf(item) === 'assignment'),
    [ctx.assignments],
  );
  const notes = useMemo(
    () => ctx.assignments.filter((item: any) => contentKindOf(item) === 'note'),
    [ctx.assignments],
  );
  const recentAssignments = assignments.slice(0, 5);
  const recentNotes = notes.slice(0, 5);

  return (
    <ScreenLayout
      title="Work"
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

      <WorkSummaryCardsRow assignmentsCount={assignments.length} notesCount={notes.length} />

      <SectionTitle>Explore by subject</SectionTitle>
      <ExploreAssignmentsTiles
        onAssignments={() => navigation.navigate('ContentSubjects', { contentKind: 'assignment' })}
        onNotes={() => navigation.navigate('ContentSubjects', { contentKind: 'note' })}
      />

      <SectionHeader
        title="Recent assignments"
        actionLabel="By subject"
        onAction={() => navigation.navigate('ContentSubjects', { contentKind: 'assignment' })}
      />
      <RecentAssignmentsCard
        assignments={recentAssignments}
        onItemPress={item => {
          if (item?.id != null) {
            navigation.navigate('AssignmentDetail', { id: Number(item.id) });
          } else {
            navigation.navigate('ContentSubjects', { contentKind: 'assignment' });
          }
        }}
      />

      <SectionHeader
        title="Recent notes"
        actionLabel="By subject"
        onAction={() => navigation.navigate('ContentSubjects', { contentKind: 'note' })}
      />
      <RecentAssignmentsCard
        assignments={recentNotes}
        onItemPress={item => {
          if (item?.id != null) {
            navigation.navigate('AssignmentDetail', { id: Number(item.id) });
          } else {
            navigation.navigate('ContentSubjects', { contentKind: 'note' });
          }
        }}
      />
    </ScreenLayout>
  );
}
