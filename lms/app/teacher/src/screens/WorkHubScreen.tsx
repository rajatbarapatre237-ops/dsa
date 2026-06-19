import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard, Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { settleApiCalls } from '../utils/apiError';
import { WorkStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

function isActiveAssignment(item: any) {
  return item.status === 1 || item.status === '1' || item.status === true;
}

function isMeaningfulAssignment(item: any) {
  const name = String(item.document_name ?? '').trim();
  const batch = String(item.batch_name ?? '').trim();
  return !!(name || batch);
}

function contentKindOf(item: any) {
  return item.content_kind === 'note' ? 'note' : 'assignment';
}

function assignmentLabel(item: any) {
  const name = String(item.document_name ?? '').trim();
  if (name) return name;
  const batch = String(item.batch_name ?? '').trim();
  if (batch) return `Item for ${batch}`;
  return 'Untitled';
}

export default function WorkHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [assignments, setAssignments] = useState<any[]>([]);
  const [tests, setTests] = useState<any[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const [assignRes, testsRes] = await settleApiCalls([
          LmsApi.assignments(),
          LmsApi.classTests(),
        ]);
        setAssignments((assignRes as any)?.assignments ?? []);
        setTests((testsRes as any)?.tests ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  const activeAssignments = assignments.filter(
    a => isActiveAssignment(a) && isMeaningfulAssignment(a) && contentKindOf(a) === 'assignment',
  );
  const activeNotes = assignments.filter(
    a => isActiveAssignment(a) && isMeaningfulAssignment(a) && contentKindOf(a) === 'note',
  );
  const recentAssignments = activeAssignments.slice(0, 3);
  const recentNotes = activeNotes.slice(0, 3);
  const recentTests = tests.slice(0, 3);

  return (
    <ScreenLayout
      title="Work"
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        <Text style={styles.summaryTitle}>Assignments, notes & class tests</Text>
        <Text style={styles.summaryHint}>Manage homework, class notes, uploads, and test marks</Text>
        <View style={styles.statsRow}>
          <View style={styles.statBox}>
            <Text style={styles.statValue}>{activeAssignments.length}</Text>
            <Text style={styles.statLabel}>Assignments</Text>
          </View>
          <View style={styles.statBox}>
            <Text style={styles.statValue}>{activeNotes.length}</Text>
            <Text style={styles.statLabel}>Notes</Text>
          </View>
          <View style={styles.statBox}>
            <Text style={styles.statValue}>{tests.length}</Text>
            <Text style={styles.statLabel}>Class tests</Text>
          </View>
        </View>
      </Card>

      <Text style={styles.section}>Assignments</Text>
      <ActionCard
        iconName="file-document-multiple-outline"
        title="View assignments"
        subtitle="Homework and uploads by subject"
        onPress={() => navigation.navigate('AssignmentsList')}
      />
      <ActionCard
        iconName="file-upload-outline"
        title="Add assignment"
        subtitle="Share a link or upload a photo for a batch"
        onPress={() => navigation.navigate('AddAssignment')}
      />

      <Text style={styles.section}>Notes</Text>
      <ActionCard
        iconName="notebook-outline"
        title="View notes"
        subtitle="Class notes shared by subject"
        onPress={() => navigation.navigate('NotesList', { contentKind: 'note' })}
      />
      <ActionCard
        iconName="camera-outline"
        title="Add note"
        subtitle="Take a photo or upload notes for a subject"
        onPress={() => navigation.navigate('AddNote', { contentKind: 'note' })}
      />

      <Text style={styles.section}>Class tests</Text>
      <ActionCard
        iconName="clipboard-plus-outline"
        title="Create class test"
        subtitle="Set up test name, date, and marks"
        onPress={() => navigation.navigate('CreateClassTest')}
      />
      <ActionCard
        iconName="pencil-box-outline"
        title="Enter test marks"
        subtitle="Record student scores after a test is created"
        onPress={() => navigation.navigate('EnterMarks')}
      />
      <ActionCard
        iconName="clipboard-text-outline"
        title="View all tests"
        subtitle="See every class test you have created"
        onPress={() => navigation.navigate('ClassTests')}
      />
      <ActionCard
        iconName="chart-box-outline"
        title="Class test results"
        subtitle="Review submitted marks and outcomes"
        onPress={() => navigation.navigate('TestResults')}
      />

      {(recentTests.length > 0 || recentAssignments.length > 0 || recentNotes.length > 0) && (
        <Text style={styles.section}>Recent</Text>
      )}

      {recentTests.length > 0 ? (
        <Card title="Latest class tests">
          {recentTests.map((test: any, index: number) => (
            <Pressable
              key={test.id}
              style={[styles.recentRow, index > 0 && styles.recentRowBorder]}
              onPress={() => navigation.navigate('ClassTests')}>
              <Text style={styles.recentTitle}>{test.test_name}</Text>
              <Text style={styles.recentSub}>
                {test.course_name} · {test.test_date}
              </Text>
            </Pressable>
          ))}
        </Card>
      ) : null}

      {recentNotes.length > 0 ? (
        <Card title="Latest notes">
          {recentNotes.map((note: any, index: number) => (
            <Pressable
              key={note.id}
              style={[styles.recentRow, index > 0 && styles.recentRowBorder]}
              onPress={() => navigation.navigate('NotesList', { contentKind: 'note' })}>
              <Text style={styles.recentTitle}>{assignmentLabel(note)}</Text>
              <Text style={styles.recentSub}>
                {[note.subject_name, note.batch_name].filter(Boolean).join(' · ') || 'No batch'}
              </Text>
            </Pressable>
          ))}
        </Card>
      ) : null}

      {recentAssignments.length > 0 ? (
        <Card title="Latest assignments">
          {recentAssignments.map((assignment: any, index: number) => (
            <Pressable
              key={assignment.id}
              style={[styles.recentRow, index > 0 && styles.recentRowBorder]}
              onPress={() => navigation.navigate('AssignmentsList')}>
              <Text style={styles.recentTitle}>{assignmentLabel(assignment)}</Text>
              <Text style={styles.recentSub}>
                {[assignment.subject_name, assignment.batch_name].filter(Boolean).join(' · ') || 'No batch'}
              </Text>
            </Pressable>
          ))}
        </Card>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  summaryTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: theme.text,
  },
  summaryHint: {
    fontSize: 13,
    color: theme.muted,
    marginTop: 4,
    marginBottom: 14,
  },
  section: {
    fontSize: 13,
    fontWeight: '700',
    color: theme.muted,
    marginBottom: 10,
    marginTop: 4,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
  },
  statsRow: {
    flexDirection: 'row',
    gap: 10,
  },
  statBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
  },
  statValue: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
  },
  statLabel: {
    fontSize: 10,
    color: theme.muted,
    marginTop: 4,
    fontWeight: '600',
    textAlign: 'center',
    paddingHorizontal: 4,
  },
  recentRow: {
    paddingVertical: 8,
  },
  recentRowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: '#e2e8f0',
    marginTop: 4,
    paddingTop: 10,
  },
  recentTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: theme.text,
  },
  recentSub: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 2,
  },
});
