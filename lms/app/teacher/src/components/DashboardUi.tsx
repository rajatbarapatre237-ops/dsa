import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

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

function attendanceTone(status?: string | null) {
  const value = (status ?? '').trim().toLowerCase();
  if (value === 'present') {
    return { label: 'Present', bg: '#dcfce7', color: '#166534', icon: 'checkmark-circle' as const };
  }
  if (value === 'absent') {
    return { label: 'Absent', bg: '#fee2e2', color: '#991b1b', icon: 'close-circle' as const };
  }
  return { label: 'No record', bg: '#f1f5f9', color: theme.muted, icon: 'help-circle' as const };
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
}: {
  date?: string | null;
  status?: string | null;
  entry?: string | null;
  exit?: string | null;
}) {
  const tone = attendanceTone(status);

  return (
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
    </View>
  );
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
});
