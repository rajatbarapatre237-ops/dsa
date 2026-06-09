import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export function ExploreAcademicsTiles({
  onCourses,
  onAttendance,
  onTransactions,
}: {
  onCourses: () => void;
  onAttendance: () => void;
  onTransactions: () => void;
}) {
  return (
    <View style={styles.tripleRow}>
      <HubTile icon="book-open-page-variant" title="Courses" onPress={onCourses} />
      <HubTile icon="calendar-check" title="Attendance" onPress={onAttendance} />
      <HubTile icon="cash-multiple" title="Fees" onPress={onTransactions} />
    </View>
  );
}

export function ExploreAssignmentsTiles({ onAssignments }: { onAssignments: () => void }) {
  return (
    <View style={styles.singleRow}>
      <HubTile icon="file-document-outline" title="All assignments" onPress={onAssignments} />
    </View>
  );
}

export function ExploreQuickTiles({
  onAcademics,
  onAssignments,
  onAccount,
}: {
  onAcademics: () => void;
  onAssignments: () => void;
  onAccount: () => void;
}) {
  return (
    <View style={styles.tripleRow}>
      <HubTile icon="school-outline" family="ionicons" title="Academics" onPress={onAcademics} />
      <HubTile icon="document-text-outline" family="ionicons" title="Work" onPress={onAssignments} />
      <HubTile icon="person-outline" family="ionicons" title="Account" onPress={onAccount} />
    </View>
  );
}

function HubTile({
  icon,
  family = 'material',
  title,
  onPress,
}: {
  icon: string;
  family?: 'ionicons' | 'material';
  title: string;
  onPress: () => void;
}) {
  return (
    <Pressable
      style={({ pressed }) => [styles.hubTile, pressed && styles.pressed]}
      onPress={onPress}>
      <View style={styles.hubIcon}>
        <AppIcon name={icon} family={family} size={20} color={PRIMARY} />
      </View>
      <Text style={styles.hubTitle}>{title}</Text>
    </Pressable>
  );
}

export function RecentAssignmentsCard({
  assignments,
  onItemPress,
}: {
  assignments: any[];
  onItemPress?: (item: any) => void;
}) {
  return (
    <View style={styles.listCard}>
      {assignments.length > 0 ? (
        assignments.map((item, index) => {
          const title = String(item.document_name ?? item.title ?? 'Assignment').trim() || 'Assignment';
          const batch = item.batch ?? item.batch_name;
          const row = (
            <View style={[styles.recentRow, index > 0 && styles.recentRowBorder]}>
              <View style={styles.recentIcon}>
                <AppIcon name="document-text-outline" size={18} color={PRIMARY} />
              </View>
              <View style={styles.recentBody}>
                <Text style={styles.recentName} numberOfLines={1}>
                  {title}
                </Text>
                <Text style={styles.recentMeta}>{batch ? String(batch) : 'Active assignment'}</Text>
              </View>
              {onItemPress ? <AppIcon name="chevron-forward" size={18} color={theme.muted} /> : null}
            </View>
          );
          if (onItemPress) {
            return (
              <Pressable key={`${item.id ?? title}-${index}`} onPress={() => onItemPress(item)}>
                {row}
              </Pressable>
            );
          }
          return <View key={`${item.id ?? title}-${index}`}>{row}</View>;
        })
      ) : (
        <Text style={styles.recentEmpty}>No assignments available right now</Text>
      )}
    </View>
  );
}

export type StoredStudentUser = {
  name?: string;
  sid?: string;
  id?: number | string;
};

function pickText(value?: string | number | null) {
  const text = String(value ?? '').trim();
  return text || null;
}

export function formatStudentDisplayId(value?: string | number | null): string | null {
  const text = String(value ?? '').trim();
  if (!text) return null;

  const upper = text.toUpperCase();
  if (upper.startsWith('DSA')) return upper;
  if (upper.startsWith('ACE')) return `DSA${upper.slice(3)}`;

  return `DSA${text}`;
}

export function resolveStudentId(profile?: any, storedUser?: StoredStudentUser | null) {
  const sid = pickText(storedUser?.sid);
  if (sid) return formatStudentDisplayId(sid);

  const rawId = storedUser?.id ?? profile?.id;
  return formatStudentDisplayId(rawId);
}

export function resolveCourseMeta({
  course,
  batch,
  profile,
  attendanceSummary,
  monthRecords,
}: {
  course?: string | null;
  batch?: string | number | null;
  profile?: any;
  attendanceSummary?: any[];
  monthRecords?: any[];
}) {
  const resolvedCourse =
    pickText(course) ??
    pickText(profile?.course_name) ??
    pickText(profile?.course) ??
    pickText(attendanceSummary?.[0]?.course) ??
    pickText(monthRecords?.[0]?.course);

  const resolvedBatch =
    pickText(batch) ??
    pickText(profile?.batch) ??
    pickText(attendanceSummary?.[0]?.batch) ??
    pickText(monthRecords?.[0]?.batch);

  return { course: resolvedCourse, batch: resolvedBatch };
}

export function attendanceFromSummary(summary: any[] | undefined) {
  const row = summary?.[0];
  if (!row) {
    return { present: 0, absent: 0, total: 0, percent: 0 };
  }
  const present = Number(row.present_days ?? 0);
  const absent = Number(row.absent_days ?? 0);
  const total = Number(row.total_days ?? 0);
  const percent = total ? Math.round((present / total) * 100) : 0;
  return { present, absent, total, percent };
}

export function monthAttendanceFromRecords(records: any[]) {
  const stats = records.reduce(
    (acc, row) => {
      const status = String(row.status ?? '').toLowerCase();
      if (status === 'present') acc.present += 1;
      if (status === 'absent') acc.absent += 1;
      acc.total += 1;
      return acc;
    },
    { present: 0, absent: 0, total: 0 },
  );
  return {
    ...stats,
    percent: stats.total ? Math.round((stats.present / stats.total) * 100) : 0,
  };
}

export function recordsForMonth(records: any[], month: string) {
  return records.filter(row => String(row.date ?? '').slice(0, 7) === month);
}

const styles = StyleSheet.create({
  tripleRow: { flexDirection: 'row', gap: 10, marginBottom: 14 },
  singleRow: { marginBottom: 14 },
  hubTile: {
    flex: 1,
    backgroundColor: theme.card,
    borderRadius: 16,
    padding: 12,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  pressed: { opacity: 0.92, transform: [{ scale: 0.98 }] },
  hubIcon: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 8,
  },
  hubTitle: { fontSize: 12, fontWeight: '800', color: theme.text, textAlign: 'center' },
  listCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  recentRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 10, gap: 10 },
  recentRowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  recentIcon: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  recentBody: { flex: 1 },
  recentName: { fontSize: 14, fontWeight: '800', color: theme.text },
  recentMeta: { fontSize: 11, color: theme.muted, marginTop: 3 },
  recentEmpty: { fontSize: 14, color: theme.muted, textAlign: 'center', paddingVertical: 12 },
});
