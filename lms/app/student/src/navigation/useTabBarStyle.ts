import { Platform, StyleSheet, ViewStyle } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useThemeColors } from '../ui/useThemeColors';

const TAB_BAR_BASE = Platform.OS === 'android' ? 56 : 52;

export function useTabBarStyle(): { tabBarStyle: ViewStyle; contentBottomPadding: number } {
  const insets = useSafeAreaInsets();
  const colors = useThemeColors();
  const bottomInset = Math.max(insets.bottom, Platform.OS === 'android' ? 8 : 0);

  return {
    tabBarStyle: {
      height: TAB_BAR_BASE + bottomInset,
      paddingBottom: bottomInset,
      paddingTop: 6,
      backgroundColor: colors.card,
      borderTopColor: colors.border,
      borderTopWidth: StyleSheet.hairlineWidth,
    },
    contentBottomPadding: TAB_BAR_BASE + bottomInset + 16,
  };
}
