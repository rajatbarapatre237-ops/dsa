import React from 'react';
import { Pressable, Text, StyleSheet, View } from 'react-native';
import { theme } from '../ui/theme';
import { PRIMARY } from '../config';

export default function ListRow({
  title,
  subtitle,
  onPress,
  right,
}: {
  title: string;
  subtitle?: string;
  onPress?: () => void;
  right?: string;
}) {
  const content = (
    <View style={styles.row}>
      <View style={styles.body}>
        <Text style={styles.title}>{title}</Text>
        {subtitle ? <Text style={styles.sub}>{subtitle}</Text> : null}
      </View>
      {right ? <Text style={styles.right}>{right}</Text> : onPress ? <Text style={styles.chevron}>›</Text> : null}
    </View>
  );
  if (!onPress) {
    return <View style={styles.wrap}>{content}</View>;
  }
  return (
    <Pressable style={styles.wrap} onPress={onPress}>
      {content}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  wrap: { borderBottomWidth: 1, borderBottomColor: '#eee', paddingVertical: 12 },
  row: { flexDirection: 'row', alignItems: 'center' },
  body: { flex: 1 },
  title: { fontSize: 15, fontWeight: '600', color: theme.text },
  sub: { fontSize: 12, color: theme.muted, marginTop: 3 },
  right: { fontSize: 13, color: PRIMARY, fontWeight: '600' },
  chevron: { fontSize: 22, color: theme.muted, paddingLeft: 8 },
});
