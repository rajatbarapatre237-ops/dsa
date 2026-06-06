import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { SectionTitle, StatGrid } from './DashboardUi';

type Chip = { label: string; icon?: string };

function attendanceTone(status?: string | null) {
  const value = (status ?? '').trim().toLowerCase();
  if (value === 'present') {
    return { label: 'Present', bg: '#dcfce7', color: '#166534', icon: 'checkmark-circle' as const };
  }
  if (value === 'absent') {
    return { label: 'Absent', bg: '#fee2e2', color: '#991b1b', icon: 'close-circle' as const };
  }
  return { label: 'Unknown', bg: '#f1f5f9', color: theme.muted, icon: 'help-circle' as const };
}

export function formatFees(value?: string | number | null) {
  const amount = Number(value);
  if (!Number.isFinite(amount)) {
    return '—';
  }
  return `₹${amount.toLocaleString('en-IN')}`;
}

export function StudentProfileHero({
  name,
  studentId,
  avatarLabel,
  chips,
}: {
  name: string;
  studentId?: string | number | null;
  avatarLabel?: string;
  chips?: Chip[];
}) {
  const initial = (avatarLabel ?? name).trim().charAt(0).toUpperCase() || '?';

  return (
    <View style={styles.heroCard}>
      <View style={styles.heroBanner}>
        <View style={styles.avatarRing}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{initial}</Text>
          </View>
        </View>
        <Text style={styles.heroEyebrow}>Student profile</Text>
        <Text style={styles.heroName}>{name}</Text>
        {studentId ? <Text style={styles.heroId}>ID {studentId}</Text> : null}
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

export function StudentQuickStats({
  studentId,
  feesBalance,
  schoolName,
}: {
  studentId?: string | number | null;
  feesBalance?: string | number | null;
  schoolName?: string | null;
}) {
  const fees = Number(feesBalance);
  const hasFees = Number.isFinite(fees) && fees > 0;

  return (
    <StatGrid
      items={[
        {
          label: 'Student ID',
          value: studentId ?? '—',
          tint: theme.primarySoft,
        },
        {
          label: 'Fees balance',
          value: formatFees(feesBalance),
          tint: hasFees ? '#fff7ed' : '#f0fdf4',
        },
        {
          label: 'School',
          value: (schoolName ?? '').trim() || 'Not set',
          tint: '#f8fafc',
        },
      ]}
    />
  );
}

export function ProfileInfoRow({
  icon,
  iconFamily = 'ionicons',
  label,
  value,
  last,
}: {
  icon: string;
  iconFamily?: 'ionicons' | 'material';
  label: string;
  value?: string | number | null;
  last?: boolean;
}) {
  const text = value === null || value === undefined || String(value).trim() === '' ? '—' : String(value);

  return (
    <View style={[styles.infoRow, last && styles.infoRowLast]}>
      <View style={styles.infoIconWrap}>
        <AppIcon name={icon} family={iconFamily} size={18} color={PRIMARY} />
      </View>
      <View style={styles.infoBody}>
        <Text style={styles.infoLabel}>{label}</Text>
        <Text style={styles.infoValue} numberOfLines={2}>
          {text}
        </Text>
      </View>
    </View>
  );
}

export function ProfileDetailsCard({
  title,
  children,
}: {
  title: string;
  children: React.ReactNode;
}) {
  return (
    <View style={styles.detailsCard}>
      <Text style={styles.detailsTitle}>{title}</Text>
      <View style={styles.detailsBody}>{children}</View>
    </View>
  );
}

export function AttendanceRecordCard({
  date,
  status,
  course,
  batch,
  last,
}: {
  date: string;
  status?: string | null;
  course?: string | null;
  batch?: string | null;
  last?: boolean;
}) {
  const tone = attendanceTone(status);
  const meta = [course, batch].filter(Boolean).join(' / ');

  return (
    <View style={[styles.attendanceRow, last && styles.attendanceRowLast]}>
      <View style={styles.attendanceDateWrap}>
        <AppIcon name="calendar-outline" size={18} color={PRIMARY} />
        <View style={styles.attendanceDateBody}>
          <Text style={styles.attendanceDate}>{date}</Text>
          {meta ? <Text style={styles.attendanceMeta}>{meta}</Text> : null}
        </View>
      </View>
      <View style={[styles.statusBadge, { backgroundColor: tone.bg }]}>
        <AppIcon name={tone.icon} size={14} color={tone.color} />
        <Text style={[styles.statusText, { color: tone.color }]}>{tone.label}</Text>
      </View>
    </View>
  );
}

export function EmptyAttendanceState() {
  return (
    <View style={styles.emptyState}>
      <View style={styles.emptyIconWrap}>
        <AppIcon name="calendar-clear-outline" size={22} color={theme.muted} />
      </View>
      <Text style={styles.emptyTitle}>No recent attendance</Text>
      <Text style={styles.emptySub}>Records will appear here once marked.</Text>
    </View>
  );
}

export function StudentProfileSectionTitle({ children }: { children: string }) {
  return <SectionTitle>{children}</SectionTitle>;
}

const styles = StyleSheet.create({
  heroCard: {
    backgroundColor: theme.card,
    borderRadius: 22,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    overflow: 'hidden',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  heroBanner: {
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    paddingTop: 24,
    paddingBottom: 20,
    paddingHorizontal: 18,
  },
  avatarRing: {
    padding: 4,
    borderRadius: 999,
    backgroundColor: 'rgba(255,255,255,0.85)',
    marginBottom: 12,
  },
  avatar: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: theme.card,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: theme.primaryMuted,
  },
  avatarText: { fontSize: 30, fontWeight: '800', color: PRIMARY },
  heroEyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  heroName: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
    marginTop: 6,
    textAlign: 'center',
  },
  heroId: {
    fontSize: 13,
    fontWeight: '700',
    color: theme.muted,
    marginTop: 6,
  },
  chipRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8,
    padding: 16,
  },
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
  detailsCard: {
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
  detailsTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: theme.text,
    marginBottom: 4,
  },
  detailsBody: { marginTop: 8 },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingVertical: 12,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  infoRowLast: { borderBottomWidth: 0, paddingBottom: 0 },
  infoIconWrap: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  infoBody: { flex: 1 },
  infoLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: theme.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: 3,
  },
  infoValue: { fontSize: 15, fontWeight: '600', color: theme.text, lineHeight: 21 },
  attendanceRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 12,
    paddingVertical: 14,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  attendanceRowLast: { borderBottomWidth: 0, paddingBottom: 0 },
  attendanceDateWrap: { flexDirection: 'row', alignItems: 'center', gap: 10, flex: 1 },
  attendanceDateBody: { flex: 1 },
  attendanceDate: { fontSize: 15, fontWeight: '700', color: theme.text },
  attendanceMeta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 7,
  },
  statusText: { fontSize: 12, fontWeight: '800' },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 28,
    paddingHorizontal: 12,
  },
  emptyIconWrap: {
    width: 52,
    height: 52,
    borderRadius: 18,
    backgroundColor: '#f8fafc',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  emptyTitle: { fontSize: 15, fontWeight: '700', color: theme.text },
  emptySub: { fontSize: 13, color: theme.muted, marginTop: 4, textAlign: 'center' },
});
