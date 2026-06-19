import { BottomTabNavigationProp } from '@react-navigation/bottom-tabs';
import { ParamListBase, StackActions } from '@react-navigation/native';

export function resetStackOnTabPress() {
  return ({
    navigation,
    route,
  }: {
    navigation: BottomTabNavigationProp<ParamListBase>;
    route: { name: string };
  }) => ({
    tabPress: () => {
      const tabRoute = navigation.getState().routes.find(r => r.name === route.name);
      const stackState = tabRoute?.state;

      if (stackState?.key != null && (stackState.index ?? 0) > 0) {
        navigation.dispatch({
          ...StackActions.popToTop(),
          target: stackState.key,
        });
      }
    },
  });
}
