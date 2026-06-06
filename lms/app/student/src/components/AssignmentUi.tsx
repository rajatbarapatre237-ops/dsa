import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export function AssignmentsSummaryCard({ count }: { count: number }) {
  return (
    <View style={styles.summaryCard}>
      <View style={styles.summaryIcon}>
        <AppIcon name="document-text-outline" family="ionicons" size={22} color={PRIMARY} />
      </View>
      <View style={styles.summaryBody}>
        <Text style={styles.summaryValue}>{count}</Text>
        <Text style={styles.summaryLabel}>
          Active assignment{count === 1 ? '' : 's'} for your batch
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
        <Text style={styles.listTitle} numberOfLines={1}>
          {title}
        </Text>
        <Text style={styles.listMeta} numberOfLines={1}>
          {[batch, isLink ? 'Link' : 'File'].filter(Boolean).join(' · ')}
        </Text>
      </View>
      <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
        <Text style={[styles.typeText, isLink ? styles.typeLinkText : styles.typeFileText]}>
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
}: {
  title?: string | null;
  type?: string | null;
  batch?: string | null;
}) {
  const name = String(title ?? 'Assignment').trim() || 'Assignment';
  const isLink = String(type ?? '').toLowerCase() === 'link';
  const batchText = String(batch ?? '').trim();

  return (
    <View style={styles.heroCard}>
      <View style={styles.heroIcon}>
        <AppIcon
          name={isLink ? 'link-outline' : 'document-text-outline'}
          family="ionicons"
          size={28}
          color={PRIMARY}
        />
      </View>
      <Text style={styles.heroTitle}>{name}</Text>
      <View style={styles.heroBadges}>
        <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
          <Text style={[styles.typeText, isLink ? styles.typeLinkText : styles.typeFileText]}>
            {isLink ? 'Link assignment' : 'File assignment'}
          </Text>
        </View>
        {batchText ? (
          <View style={styles.batchBadge}>
            <Text style={styles.batchText}>{batchText}</Text>
          </View>
        ) : null}
      </View>
    </View>
  );
}

export function AssignmentDetailRow({
  label,
  value,
}: {
  label: string;
  value?: string | number | null;
}) {
  const text = value != null && String(value).trim() !== '' ? String(value) : '—';
  return (
    <View style={styles.detailRow}>
      <Text style={styles.detailLabel}>{label}</Text>
      <Text style={styles.detailValue} numberOfLines={3}>
        {text}
      </Text>
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
  summaryValue: { fontSize: 28, fontWeight: '800', color: theme.text },
  summaryLabel: { fontSize: 13, color: theme.muted, marginTop: 4 },
  listItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    gap: 10,
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
  listTitle: { fontSize: 15, fontWeight: '800', color: theme.text },
  listMeta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  typeBadge: {
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  typeLink: { backgroundColor: '#e0f2fe' },
  typeFile: { backgroundColor: '#ede9fe' },
  typeText: { fontSize: 10, fontWeight: '800' },
  typeLinkText: { color: PRIMARY },
  typeFileText: { color: '#6d28d9' },
  heroCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 20,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  heroIcon: {
    width: 64,
    height: 64,
    borderRadius: 20,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 14,
  },
  heroTitle: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
    textAlign: 'center',
    lineHeight: 28,
  },
  heroBadges: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8,
    marginTop: 12,
  },
  batchBadge: {
    backgroundColor: theme.primaryMuted,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  batchText: { fontSize: 11, fontWeight: '700', color: PRIMARY },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    gap: 16,
    paddingVertical: 12,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  detailLabel: { fontSize: 13, fontWeight: '600', color: theme.muted, flex: 1 },
  detailValue: {
    fontSize: 14,
    fontWeight: '700',
    color: theme.text,
    flex: 1.2,
    textAlign: 'right',
  },
});
