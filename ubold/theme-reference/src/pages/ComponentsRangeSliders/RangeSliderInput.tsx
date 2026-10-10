import type { BootstrapSliderItem } from './ComponentsRangeSliders.types';
import { getBootstrapSliderInputAttributes } from './useRangeSlidersRuntime';

interface RangeSliderInputProps {
  item: BootstrapSliderItem;
  name: string;
}

export default function RangeSliderInput({ item, name }: RangeSliderInputProps): JSX.Element {
  return (
    <div className={`slider slider-${item.variant}`}>
      <input type="text" name={name} value="" readOnly {...getBootstrapSliderInputAttributes(item)} />
    </div>
  );
}
