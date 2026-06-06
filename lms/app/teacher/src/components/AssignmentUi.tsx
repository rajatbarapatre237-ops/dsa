import React from 'react';
import { View, Text, StyleSheet, Pressable, Switch } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { platformWeight } from '../ui/typography';

export function AssignmentsSummaryCard({ count }: { count: number }) {
  return (
    <View style={styles.summaryCard}>
      <View style={styles.summaryIcon}>
        <AppIcon name="document-text-outline" family="ionicons" size={22} color={PRIMARY} />
      </View>
      <View style={styles.summaryBody}>
        <Text style={[styles.summaryValue, platformWeight('800')]}>{count}</Text>
        <Text style={styles.summaryLabel}>
          Assignment{count === 1 ? '' : 's'} for your batches
        </Text>
      </View>
    </View>
  );
}

export function AssignmentListItem({
  item,
  onPress,
}: {
  item: any;
  onPress: () => void;
}) {
  const title = String(item.document_name ?? 'Assignment').trim() || 'Assignment';
  const batch = String(item.batch_name ?? item.batch ?? '').trim();
  const isLink = String(item.type ?? '').toLowerCase() === 'link';
  const isActive = item.status === 1 || item.status === '1' || item.status === true;

  return (
    <Pressable style={({ pressed }) => [styles.listItem, pressed && styles.pressed]} onPress={onPress}>
      <View style={styles.listIcon}>
        <AppIcon
          name={isLink ? 'link-outline' : 'document-attach-outline'}
          family="ionicons"
          size={20}
          color={PRIMARY}
        />
      </View>
      <View style={styles.listBody}>
        <Text style={[styles.listTitle, platformWeight('800')]} numberOfLines={1}>
          {title}
        </Text>
        <Text style={styles.listMeta} numberOfLines={1}>
          {[batch, isLink ? 'Link' : 'File', isActive ? 'Active' : 'Hidden'].filter(Boolean).join(' · ')}
        </Text>
      </View>
      <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
        <Text style={[styles.typeText, platformWeight('800'), isLink ? styles.typeLinkText : styles.typeFileText]}>
          {isLink ? 'Link' : 'File'}
        </Text>
      </View>
      <AppIcon name="chevron-forward" size={18} color={theme.muted} />
    </Pressable>
  );
}

export function AssignmentHeroCard({
  title,
  type,
  batch,
  active,
}: {
  title?: string | null;
  type?: string | null;
  batch?: string | null;
  active?: boolean;
}) {
  const name = String(title ?? 'Assignment').trim() || 'Assignment';
  const isLink = String(type ?? '').toLowerCase() === 'link';
  const batchText = String(batch ?? '').trim();

  return (
    <View style={styles.heroCard}>
      <View style={styles.heroBanner}>
        <View style={styles.heroIcon}>
          <AppIcon
            name={isLink ? 'link-outline' : 'document-text-outline'}
            family="ionicons"
            size={30}
            color={PRIMARY}
          />
        </View>
        <Text style={styles.heroEyebrow}>Assignment</Text>
        <Text style={[styles.heroTitle, platformWeight('800')]}>{name}</Text>
      </View>
      <View style={styles.heroBadges}>
        <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
          <Text style={[styles.typeText, platformWeight('700'), isLink ? styles.typeLinkText : styles.typeFileText]}>
            {isLink ? 'Link assignment' : 'File assignment'}
          </Text>
        </View>
        {batchText ? (
          <View style={styles.batchBadge}>
            <AppIcon name="people-outline" size={12} color={PRIMARY} style={styles.batchIcon} />
            <Text style={[styles.batchText, platformWeight('700')]}>{batchText}</Text>
          </View>
        ) : null}
        <View style={[styles.statusBadge, active ? styles.statusActive : styles.statusHidden]}>
          <AppIcon
            name={active ? 'checkmark-circle' : 'eye-off-outline'}
            size={12}
            color={active ? theme.success : theme.muted}
          />
          <Text style={[styles.statusText, platformWeight('700'), active ? styles.statusActiveText : styles.statusHiddenText]}>
            {active ? 'Active' : 'Hidden'}
          </Text>
        </View>
      </View>
    </View>
  );
}

export function AssignmentInfoRow({
  icon,
  label,
  value,
  last,
}: {
  icon: string;
  label: string;
  value?: string | number | null;
  last?: boolean;
}) {
  const text = value != null && String(value).trim() !== '' ? String(value) : '—';

  return (
    <View style={[styles.infoRow, last && styles.infoRowLast]}>
      <View style={styles.infoIconWrap}>
        <AppIcon name={icon} family="ionicons" size={18} color={PRIMARY} />
      </View>
      <View style={styles.infoBody}>
        <Text style={[styles.infoLabel, platformWeight('700')]}>{label}</Text>
        <Text style={[styles.infoValue, platformWeight('600')]} numberOfLines={4}>
          {text}
        </Text>
      </View>
    </View>
  );
}

export function AssignmentStatusToggle({
  active,
  onChange,
}: {
  active: boolean;
  onChange: (value: boolean) => void;
}) {
  return (
    <View style={styles.toggleCard}>
      <View style={styles.toggleIconWrap}>
        <AppIcon name="toggle-outline" family="ionicons" size={20} color={PRIMARY} />
      </View>
      <View style={styles.toggleBody}>
        <Text style={[styles.toggleTitle, platformWeight('800')]}>Visibility</Text>
        <Text style={styles.toggleSub}>
          {active ? 'Students can see this assignment' : 'Hidden from students'}
        </Text>
      </View>
      <Switch value={active} onValueChange={onChange} trackColor={{ true: PRIMARY, false: '#cbd5e1' }} />
    </View>
  );
}

const styles = StyleSheet.create({
  summaryCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    gap: 14,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  summaryIcon: {
    width: 50,
    height: 50,
    borderRadius: 16,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  summaryBody: { flex: 1 },
  summaryValue: { fontSize: 28, color: theme.text },
  summaryLabel: { fontSize: 13, color: theme.muted, marginTop: 4 },
  listItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    gap: 10,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  pressed: { opacity: 0.92 },
  listIcon: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  listBody: { flex: 1 },
  listTitle: { fontSize: 15, color: theme.text },
  listMeta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  typeBadge: {
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  typeLink: { backgroundColor: '#e0f2fe' },
  typeFile: { backgroundColor: '#ede9fe' },
  typeText: { fontSize: 10 },
  typeLinkText: { color: PRIMARY },
  typeFileText: { color: '#6d28d9' },
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
  heroIcon: {
    width: 68,
    height: 68,
    borderRadius: 22,
    backgroundColor: theme.card,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
    borderWidth: 2,
    borderColor: theme.primaryMuted,
  },
  heroEyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  heroTitle: {
    fontSize: 22,
    color: theme.text,
    marginTop: 6,
    textAlign: 'center',
    lineHeight: 28,
  },
  heroBadges: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8,
    padding: 16,
  },
  batchBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.primaryMuted,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  batchIcon: { marginRight: 4 },
  batchText: { fontSize: 11, color: PRIMARY },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  statusActive: { backgroundColor: '#dcfce7' },
  statusHidden: { backgroundColor: '#f1f5f9' },
  statusText: { fontSize: 11 },
  statusActiveText: { color: theme.success },
  statusHiddenText: { color: theme.muted },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
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
    color: theme.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: 3,
  },
  infoValue: { fontSize: 15, color: theme.text, lineHeight: 21 },
  toggleCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 18,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    gap: 12,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 2,
  },
  toggleIconWrap: {
    width: 44,
    height: 44,
    borderRadius: 14,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  toggleBody: { flex: 1 },
  toggleTitle: { fontSize: 15, color: theme.text },
  toggleSub: { fontSize: 12, color: theme.muted, marginTop: 3, lineHeight: 17 },
});
