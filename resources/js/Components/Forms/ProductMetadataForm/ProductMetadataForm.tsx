import { useId } from "react";
import FormField from "@/Components/Forms/FormField/FormField";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import TextArea from "@/Components/Forms/TextArea/TextArea";
import ProductFormSection from "@/Components/Forms/ProductFormSection/ProductFormSection";
import { metadataFields } from "./ProductMetadataForm.data";
export default function ProductMetadataForm(): JSX.Element {
  const id = useId();
  return (
    <ProductFormSection title="Metadados">
      {metadataFields.map((field) => (
        <FormField
          id={`${id}-${field.name}`}
          key={field.name}
          label={field.label}
        >
          {field.rows ? (
            <TextArea
              id={`${id}-${field.name}`}
              name={field.name}
              placeholder={field.placeholder}
              rows={field.rows}
            />
          ) : (
            <TextInput
              id={`${id}-${field.name}`}
              name={field.name}
              placeholder={field.placeholder}
            />
          )}
        </FormField>
      ))}
    </ProductFormSection>
  );
}
