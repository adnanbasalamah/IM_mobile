---
name: Retail Operations Precision
colors:
  surface: '#f8f9fb'
  surface-dim: '#d9dadc'
  surface-bright: '#f8f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f6'
  surface-container: '#edeef0'
  surface-container-high: '#e7e8ea'
  surface-container-highest: '#e1e2e4'
  on-surface: '#191c1e'
  on-surface-variant: '#444653'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f3'
  outline: '#757684'
  outline-variant: '#c4c5d5'
  surface-tint: '#3755c3'
  primary: '#00288e'
  on-primary: '#ffffff'
  primary-container: '#1e40af'
  on-primary-container: '#a8b8ff'
  inverse-primary: '#b8c4ff'
  secondary: '#555f70'
  on-secondary: '#ffffff'
  secondary-container: '#d6e0f4'
  on-secondary-container: '#596374'
  tertiary: '#003d28'
  on-tertiary: '#ffffff'
  tertiary-container: '#00563a'
  on-tertiary-container: '#5bcf9e'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b8c4ff'
  on-primary-fixed: '#001453'
  on-primary-fixed-variant: '#173bab'
  secondary-fixed: '#d9e3f7'
  secondary-fixed-dim: '#bdc7db'
  on-secondary-fixed: '#121c2a'
  on-secondary-fixed-variant: '#3d4757'
  tertiary-fixed: '#85f8c4'
  tertiary-fixed-dim: '#68dba9'
  on-tertiary-fixed: '#002114'
  on-tertiary-fixed-variant: '#005137'
  background: '#f8f9fb'
  on-background: '#191c1e'
  surface-variant: '#e1e2e4'
typography:
  display:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  header-section:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-main:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  data-entry:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  caption:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  container-padding: 16px
  element-gap: 12px
  input-height: 48px
  touch-target-min: 44px
---

## Brand & Style
The design system is engineered for high-stakes retail environments where speed, accuracy, and reliability are paramount. The brand personality is utilitarian and professional, aiming to instill confidence in cashiers and store managers handling financial data. 

The aesthetic follows a **Corporate / Modern** style with a heavy emphasis on **Minimalism**. By prioritizing white space and a restricted color palette, the system reduces cognitive load during fast-paced transactions. The UI uses subtle depth to separate actionable elements from static data, ensuring that the user's focus remains on the "point of work."

## Colors
The palette is dominated by "Trustworthy Blue" (#1E40AF), used strategically for primary actions and brand presence. 

- **Primary:** Used for main call-to-action buttons, active states, and headers.
- **Secondary:** A deep charcoal grey (#374151) used for primary text to ensure maximum legibility against white backgrounds.
- **Success (Tertiary):** A crisp green (#059669) reserved for balanced cash reports and completed transactions.
- **Neutrals:** A range of grays provides structural scaffolding. Backgrounds are kept at pure white (#FFFFFF) for maximum contrast, while surfaces like input fields use a subtle stroke to define boundaries without clutter.

## Typography
This design system utilizes **Inter** for its exceptional legibility and systematic performance in data-heavy environments. The typographic scale is optimized for mobile screens, ensuring that numerical data—such as cash counts and register totals—is prominent and unmistakable.

A "Data Entry" style is defined specifically for input fields, using a medium weight to ensure clarity even under harsh retail lighting. All labels use a high-contrast treatment to assist in rapid form scanning.

## Layout & Spacing
The layout employs a **Fluid Grid** model specifically tuned for mobile devices. It utilizes 16px side margins to prevent content from hitting the edge of the screen, with a standard 12px gutter between vertical elements.

To facilitate rapid "thumb-driven" interaction, all interactive elements maintain a minimum height of 48px. Spacing follows a 4px base unit, ensuring consistent rhythm between labels, inputs, and their respective groupings.

## Elevation & Depth
Depth in this design system is communicated through **Ambient Shadows** and tonal layering. 

- **Surface Level 0:** The main application background (Pure White).
- **Surface Level 1:** Cards and containers use a subtle 1px border (#E5E7EB) with a very soft, diffused shadow (0px 2px 4px rgba(0,0,0,0.05)) to lift them slightly from the background.
- **Surface Level 2:** Active modals and pop-overs use a more pronounced shadow to focus the user’s attention on the immediate task, such as confirming a cash drop.
- **Interaction:** Buttons utilize a slight inner-glow on press to simulate physical feedback.

## Shapes
The shape language is defined by a consistent **8px (0.5rem) corner radius**. This "Rounded" approach softens the professional aesthetic, making the tool feel accessible while maintaining a structured, modular look. 

Buttons, input fields, and card containers all share this 8px radius to create a unified visual language. Circular shapes are reserved exclusively for user profile avatars and status indicators.

## Components

### Input Fields
Inputs are the core of this system. They feature a 2px high-contrast border when focused (#1E40AF) and include clear "Clear" (X) icons to allow users to quickly fix data entry errors. Labels are always persistent (not floating) to ensure the user never loses context of what they are typing.

### Buttons
- **Primary:** Solid "Trustworthy Blue" with white text.
- **Secondary:** White background with a grey border for less critical actions like "Cancel" or "Back."
- **Full-width:** On mobile, primary actions are full-width at the bottom of the screen for easy thumb access.

### Lists & Data Rows
Used for transaction history and cash logs. Each row has a minimum height of 56px, featuring outline icons for "Money" (cash flow) or "Person" (user logs) on the left, and the numerical value in bold on the right.

### Value Chips
Small, rounded-pill containers used to display status (e.g., "Verified," "Pending"). These use low-saturation background tints of the success or warning colors to remain legible without distracting from primary data.

### Money Input (Specialized)
A custom component featuring a large-format text entry with a fixed currency prefix. This component uses a larger font size (Display) to emphasize the financial value being entered.