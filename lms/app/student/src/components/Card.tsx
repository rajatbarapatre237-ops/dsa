import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { theme } from '../ui/theme';
import { PRIMARY } from '../config';

type Props = {
  title?: string;
  children: React.ReactNode;
  onPress?: () => void;
};

export function Card({ title, children, onPress }: Props) {
  const inner = (
    <View style={styles.card}>
      {title ? <Text style={styles.title}>{title}</Text> : null}
      {children}
    </View>
  );
  if (onPress) {
    return <Pressable onPress={onPress}>{inner}</Pressable>;
  }
  return inner;
}

export function MenuTile({
  title,
  subtitle,
  onPress,
}: {
  title: string;
  subtitle?: string;
  onPress: () => void;
}) {
  return (
    <Pressable style={styles.tile} onPress={onPress}>
      <View style={styles.tileBar} />
      <View style={styles.tileBody}>
        <Text style={styles.tileTitle}>{title}</Text>
        {subtitle ? <Text style={styles.tileSub}>{subtitle}</Text> : null}
      </View>
      <Text style={styles.chevron}>›</Text>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: theme.card,
    borderRadius: 14,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 2,
  },
  title: { fontSize: 16, fontWeight: '700', color: theme.text, marginBottom: 10 },
  tile: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 12,
    marginBottom: 10,
    overflow: 'hidden',
  },
  tileBar: { width: 4, alignSelf: 'stretch', backgroundColor: PRIMARY },
  tileBody: { flex: 1, padding: 14 },
  tileTitle: { fontSize: 15, fontWeight: '600', color: theme.text },
  tileSub: { fontSize: 12, color: theme.muted, marginTop: 2 },
  chevron: { fontSize: 22, color: theme.muted, paddingRight: 14 },
});
