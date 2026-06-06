import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export type AttendanceRecord = {
  date: string;
  status?: string | null;
  entry_time?: string | null;
  exit_time?: string | null;
};

export function SectionTitle({ children }: { children: string }) {
  return <Text style={styles.sectionTitle}>{children}</Text>;
}

export function HeroCard({
  eyebrow,
  title,
  subtitle,
  avatarLabel,
  chips,
}: {
  eyebrow: string;
  title: string;
  subtitle?: string;
  avatarLabel?: string;
  chips?: { label: string; icon?: string }[];
}) {
  const initial = (avatarLabel ?? title).trim().charAt(0).toUpperCase() || '?';

  return (
    <View style={styles.hero}>
      <View style={styles.heroTop}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{initial}</Text>
        </View>
        <View style={styles.heroText}>
          <Text style={styles.eyebrow}>{eyebrow}</Text>
          <Text style={styles.heroTitle} numberOfLines={2}>
            {title}
          </Text>
          {subtitle ? <Text style={styles.heroSub}>{subtitle}</Text> : null}
        </View>
      </View>
      {chips && chips.length > 0 ? (
        <View style={styles.chipRow}>
          {chips.map(chip => (
            <View key={chip.label} style={styles.chip}>
              {chip.icon ? (
                <AppIcon name={chip.icon} size={14} color={PRIMARY} style={styles.chipIcon} />
              ) : null}
              <Text style={styles.chipText} numberOfLines={1}>
                {chip.label}
              </Text>
            </View>
          ))}
        </View>
      ) : null}
    </View>
  );
}

export function attendanceTone(status?: string | null) {
  const value = (status ?? '').trim().toLowerCase();
  if (value === 'present') {
    return { label: 'Present', bg: '#dcfce7', color: '#166534', icon: 'checkmark-circle' as const };
  }
  if (value === 'absent') {
    return { label: 'Absent', bg: '#fee2e2', color: '#991b1b', icon: 'close-circle' as const };
  }
  return { label: 'No record', bg: '#f1f5f9', color: theme.muted, icon: 'help-circle' as const };
}

export function monthAttendanceStats(records: AttendanceRecord[]) {
  const present = records.filter(r => (r.status ?? '').trim().toLowerCase() === 'present').length;
  const absent = records.filter(r => (r.status ?? '').trim().toLowerCase() === 'absent').length;
  const total = records.length;
  const percent = total ? Math.round((present / total) * 100) : 0;
  return { present, absent, total, percent };
}

export function StudentContextCard({
  name,
  course,
  batch,
}: {
  name?: string | null;
  course?: string | null;
  batch?: string | null;
}) {
  const initial = (name ?? 'S').trim().charAt(0).toUpperCase();

  return (
    <View style={styles.contextCard}>
      <View style={styles.contextAvatar}>
        <Text style={styles.contextAvatarText}>{initial}</Text>
      </View>
      <View style={styles.contextBody}>
        <Text style={styles.contextName}>{name ?? 'Your child'}</Text>
        <Text style={styles.contextMeta}>
          {[course, batch ? `Batch ${batch}` : null].filter(Boolean).join(' · ') || 'Course details —'}
        </Text>
      </View>
      <View style={styles.contextBadge}>
        <AppIcon name="calendar" size={16} color={PRIMARY} />
      </View>
    </View>
  );
}

function formatTime(value?: string | null) {
  const text = (value ?? '').trim();
  if (!text || text.toLowerCase() === 'not available') {
    return '—';
  }
  return text;
}

export function AttendanceOverviewCard({
  date,
  status,
  entry,
  exit,
  onPress,
}: {
  date?: string | null;
  status?: string | null;
  entry?: string | null;
  exit?: string | null;
  onPress?: () => void;
}) {
  const tone = attendanceTone(status);

  const content = (
    <View style={styles.attendanceCard}>
      <View style={styles.attendanceHeader}>
        <View>
          <Text style={styles.attendanceTitle}>Today&apos;s attendance</Text>
          <Text style={styles.attendanceDate}>{date ?? '—'}</Text>
        </View>
        <View style={[styles.statusBadge, { backgroundColor: tone.bg }]}>
          <AppIcon name={tone.icon} size={14} color={tone.color} />
          <Text style={[styles.statusText, { color: tone.color }]}>{tone.label}</Text>
        </View>
      </View>
      <View style={styles.metricRow}>
        <View style={styles.metricBox}>
          <AppIcon name="log-in-outline" size={18} color={PRIMARY} />
          <Text style={styles.metricLabel}>Entry</Text>
          <Text style={styles.metricValue}>{formatTime(entry)}</Text>
        </View>
        <View style={styles.metricDivider} />
        <View style={styles.metricBox}>
          <AppIcon name="log-out-outline" size={18} color={PRIMARY} />
          <Text style={styles.metricLabel}>Exit</Text>
          <Text style={styles.metricValue}>{formatTime(exit)}</Text>
        </View>
      </View>
      {onPress ? (
        <View style={styles.viewDetailsRow}>
          <Text style={styles.viewDetailsText}>View full details</Text>
          <AppIcon name="chevron-forward" size={16} color={PRIMARY} />
        </View>
      ) : null}
    </View>
  );

  if (onPress) {
    return (
      <Pressable onPress={onPress} style={({ pressed }) => pressed && styles.pressedCard}>
        {content}
      </Pressable>
    );
  }
  return content;
}

export function MonthAttendanceSummary({
  monthLabel,
  present,
  absent,
  total,
  percent,
}: {
  monthLabel: string;
  present: number;
  absent: number;
  total: number;
  percent: number;
}) {
  return (
    <View style={styles.monthCard}>
      <View style={styles.monthHeader}>
        <View>
          <Text style={styles.monthEyebrow}>This month</Text>
          <Text style={styles.monthTitle}>{monthLabel}</Text>
        </View>
        <View style={styles.percentBubble}>
          <Text style={styles.percentValue}>{percent}%</Text>
          <Text style={styles.percentLabel}>Present</Text>
        </View>
      </View>
      <View style={styles.progressTrack}>
        <View style={[styles.progressFill, { width: `${Math.min(100, percent)}%` }]} />
      </View>
      <View style={styles.monthStats}>
        <View style={[styles.monthStat, styles.monthStatPresent]}>
          <Text style={[styles.monthStatValue, { color: theme.success }]}>{present}</Text>
          <Text style={styles.monthStatLabel}>Present</Text>
        </View>
        <View style={[styles.monthStat, styles.monthStatAbsent]}>
          <Text style={[styles.monthStatValue, { color: theme.danger }]}>{absent}</Text>
          <Text style={styles.monthStatLabel}>Absent</Text>
        </View>
        <View style={styles.monthStat}>
          <Text style={styles.monthStatValue}>{total}</Text>
          <Text style={styles.monthStatLabel}>Days logged</Text>
        </View>
      </View>
    </View>
  );
}

export function RecentAttendanceCard({
  records,
  onViewAll,
}: {
  records: AttendanceRecord[];
  onViewAll?: () => void;
}) {
  return (
    <View style={styles.recentCard}>
      <View style={styles.recentHeader}>
        <Text style={styles.recentTitle}>Recent days</Text>
        {onViewAll ? (
          <Pressable onPress={onViewAll} hitSlop={8}>
            <Text style={styles.recentLink}>See all</Text>
          </Pressable>
        ) : null}
      </View>
      {records.length > 0 ? (
        records.map((record, index) => {
          const tone = attendanceTone(record.status);
          return (
            <View
              key={`${record.date}-${index}`}
              style={[styles.recentRow, index > 0 && styles.recentRowBorder]}>
              <View style={styles.recentDateCol}>
                <Text style={styles.recentDate}>{formatShortDate(record.date)}</Text>
                <Text style={styles.recentWeekday}>{formatWeekday(record.date)}</Text>
              </View>
              <View style={[styles.recentBadge, { backgroundColor: tone.bg }]}>
                <Text style={[styles.recentBadgeText, { color: tone.color }]}>{tone.label}</Text>
              </View>
              <View style={styles.recentTimes}>
                <Text style={styles.recentTimeText}>In {formatTime(record.entry_time)}</Text>
                <Text style={styles.recentTimeText}>Out {formatTime(record.exit_time)}</Text>
              </View>
            </View>
          );
        })
      ) : (
        <Text style={styles.recentEmpty}>No attendance records this month yet</Text>
      )}
    </View>
  );
}

export function ExploreAttendanceTiles({
  onToday,
  onMonthly,
}: {
  onToday: () => void;
  onMonthly: () => void;
}) {
  return (
    <View style={styles.exploreRow}>
      <Pressable
        style={({ pressed }) => [styles.exploreTile, styles.exploreTileToday, pressed && styles.pressedTile]}
        onPress={onToday}>
        <View style={[styles.exploreIcon, { backgroundColor: '#e0f2fe' }]}>
          <AppIcon name="calendar-today" family="material" size={22} color={PRIMARY} />
        </View>
        <Text style={styles.exploreTitle}>Today</Text>
        <Text style={styles.exploreSub}>Entry & exit times</Text>
      </Pressable>
      <Pressable
        style={({ pressed }) => [styles.exploreTile, styles.exploreTileMonth, pressed && styles.pressedTile]}
        onPress={onMonthly}>
        <View style={[styles.exploreIcon, { backgroundColor: '#ede9fe' }]}>
          <AppIcon name="calendar-month" family="material" size={22} color={PRIMARY} />
        </View>
        <Text style={styles.exploreTitle}>Monthly</Text>
        <Text style={styles.exploreSub}>Full history</Text>
      </Pressable>
    </View>
  );
}

function formatShortDate(value: string) {
  const date = new Date(`${value}T12:00:00`);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString('en-IN', { day: '2-digit', month: 'short' });
}

function formatWeekday(value: string) {
  const date = new Date(`${value}T12:00:00`);
  if (Number.isNaN(date.getTime())) return '';
  return date.toLocaleString('en-IN', { weekday: 'short' });
}

export function StatGrid({ items }: { items: { label: string; value: string | number; tint?: string }[] }) {
  return (
    <View style={styles.statGrid}>
      {items.map(item => (
        <View key={item.label} style={[styles.statItem, item.tint ? { backgroundColor: item.tint } : null]}>
          <Text style={styles.statValue}>{item.value}</Text>
          <Text style={styles.statLabel}>{item.label}</Text>
        </View>
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  sectionTitle: {
    fontSize: 12,
    fontWeight: '800',
    color: theme.muted,
    marginBottom: 10,
    marginTop: 4,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  hero: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  heroTop: { flexDirection: 'row', alignItems: 'center', gap: 14 },
  avatar: {
    width: 56,
    height: 56,
    borderRadius: 18,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: { fontSize: 24, fontWeight: '800', color: PRIMARY },
  heroText: { flex: 1 },
  eyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
    marginBottom: 4,
  },
  heroTitle: { fontSize: 20, fontWeight: '800', color: theme.text, lineHeight: 26 },
  heroSub: { fontSize: 13, color: theme.muted, marginTop: 4 },
  chipRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 14 },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.primaryMuted,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 7,
    maxWidth: '100%',
  },
  chipIcon: { marginRight: 6 },
  chipText: { fontSize: 12, fontWeight: '700', color: PRIMARY },
  attendanceCard: {
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
  attendanceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
    gap: 12,
  },
  attendanceTitle: { fontSize: 16, fontWeight: '800', color: theme.text },
  attendanceDate: { fontSize: 13, color: theme.muted, marginTop: 4 },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 7,
  },
  statusText: { fontSize: 12, fontWeight: '800' },
  metricRow: {
    flexDirection: 'row',
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    paddingVertical: 14,
  },
  metricBox: { flex: 1, alignItems: 'center', paddingHorizontal: 8 },
  metricDivider: { width: 1, backgroundColor: theme.border },
  metricLabel: { fontSize: 11, fontWeight: '700', color: theme.muted, marginTop: 6 },
  metricValue: { fontSize: 14, fontWeight: '700', color: theme.text, marginTop: 2 },
  statGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10, marginBottom: 14 },
  statItem: {
    flexGrow: 1,
    flexBasis: '47%',
    backgroundColor: theme.card,
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
  },
  statValue: { fontSize: 24, fontWeight: '800', color: theme.text },
  statLabel: { fontSize: 12, color: theme.muted, marginTop: 4, textAlign: 'center' },
  pressedCard: { opacity: 0.94, transform: [{ scale: 0.995 }] },
  viewDetailsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    marginTop: 14,
    paddingTop: 12,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  viewDetailsText: { fontSize: 13, fontWeight: '700', color: PRIMARY },
  contextCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 18,
    padding: 14,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    gap: 12,
  },
  contextAvatar: {
    width: 44,
    height: 44,
    borderRadius: 14,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  contextAvatarText: { fontSize: 18, fontWeight: '800', color: PRIMARY },
  contextBody: { flex: 1 },
  contextName: { fontSize: 16, fontWeight: '800', color: theme.text },
  contextMeta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  contextBadge: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: theme.primaryMuted,
    alignItems: 'center',
    justifyContent: 'center',
  },
  monthCard: {
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
  monthHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 14,
  },
  monthEyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  monthTitle: { fontSize: 18, fontWeight: '800', color: theme.text, marginTop: 4 },
  percentBubble: {
    alignItems: 'center',
    backgroundColor: '#ecfdf3',
    borderRadius: 14,
    paddingHorizontal: 14,
    paddingVertical: 10,
    minWidth: 72,
  },
  percentValue: { fontSize: 22, fontWeight: '800', color: theme.success },
  percentLabel: { fontSize: 10, fontWeight: '700', color: theme.success, marginTop: 2 },
  progressTrack: {
    height: 8,
    backgroundColor: '#f1f5f9',
    borderRadius: 999,
    overflow: 'hidden',
    marginBottom: 14,
  },
  progressFill: {
    height: '100%',
    backgroundColor: theme.success,
    borderRadius: 999,
  },
  monthStats: { flexDirection: 'row', gap: 8 },
  monthStat: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    paddingVertical: 12,
    alignItems: 'center',
  },
  monthStatPresent: { backgroundColor: '#ecfdf3' },
  monthStatAbsent: { backgroundColor: '#fef2f2' },
  monthStatValue: { fontSize: 20, fontWeight: '800', color: theme.text },
  monthStatLabel: { fontSize: 10, fontWeight: '700', color: theme.muted, marginTop: 4 },
  recentCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
  },
  recentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  recentTitle: { fontSize: 16, fontWeight: '800', color: theme.text },
  recentLink: { fontSize: 13, fontWeight: '700', color: PRIMARY },
  recentRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
    gap: 10,
  },
  recentRowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  recentDateCol: { width: 54 },
  recentDate: { fontSize: 14, fontWeight: '800', color: theme.text },
  recentWeekday: { fontSize: 11, color: theme.muted, marginTop: 2 },
  recentBadge: {
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 5,
    minWidth: 78,
    alignItems: 'center',
  },
  recentBadgeText: { fontSize: 11, fontWeight: '800' },
  recentTimes: { flex: 1, alignItems: 'flex-end' },
  recentTimeText: { fontSize: 11, color: theme.muted, fontWeight: '600' },
  recentEmpty: { fontSize: 14, color: theme.muted, textAlign: 'center', paddingVertical: 12 },
  exploreRow: { flexDirection: 'row', gap: 12, marginBottom: 8 },
  exploreTile: {
    flex: 1,
    backgroundColor: theme.card,
    borderRadius: 18,
    padding: 16,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'flex-start',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
  },
  exploreTileToday: { borderColor: '#bae6fd' },
  exploreTileMonth: { borderColor: '#ddd6fe' },
  pressedTile: { opacity: 0.92, transform: [{ scale: 0.98 }] },
  exploreIcon: {
    width: 44,
    height: 44,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  exploreTitle: { fontSize: 15, fontWeight: '800', color: theme.text },
  exploreSub: { fontSize: 12, color: theme.muted, marginTop: 4 },
});
