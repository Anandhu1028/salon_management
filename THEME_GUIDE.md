# SalonPro UI Theme Guide

## Overview
The SalonPro Management System uses a modern, vibrant UI theme built with carefully crafted color gradients, smooth transitions, and consistent spacing. This guide explains how to properly use and customize the theme.

## Color Palette

### Primary Colors
- **Primary (Indigo)**: `#6366F1` - Main brand color for buttons, links, and primary actions
- **Primary Light**: `#E0E7FF` - Light background for primary elements
- **Primary Gradient**: `linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%)`

### Secondary Colors
- **Violet**: `#8B5CF6` - Secondary accent color
- **Violet Light**: `#F3E8FF` - Light background for violet elements
- **Violet Gradient**: `linear-gradient(135deg, #8B5CF6 0%, #D946EF 100%)`

### Status Colors
- **Success**: `#10B981` - For positive actions and completed states
- **Success Light**: `#D1FAE5` - Light background
- **Success Gradient**: `linear-gradient(135deg, #10B981 0%, #14B8A6 100%)`

- **Warning**: `#F59E0B` - For pending and in-progress states
- **Warning Light**: `#FEF3C7` - Light background
- **Warning Gradient**: `linear-gradient(135deg, #F59E0B 0%, #F97316 100%)`

- **Danger**: `#EF4444` - For destructive actions and errors
- **Danger Light**: `#FEE2E2` - Light background
- **Danger Gradient**: `linear-gradient(135deg, #EF4444 0%, #DC2626 100%)`

- **Info**: `#0EA5E9` - For informational elements
- **Info Light**: `#CFFAFE` - Light background
- **Info Gradient**: `linear-gradient(135deg, #0EA5E9 0%, #06B6D4 100%)`

### Accent Colors
- **Accent (Pink)**: `#EC4899` - For special highlights and secondary actions
- **Accent Light**: `#FCE7F3` - Light background

### Neutral Colors
- **Background**: `#F8FAFC` - Page background
- **Surface**: `#FFFFFF` - Card and container background
- **Border**: `#E2E8F0` - Primary border color
- **Border Light**: `#F1F5F9` - Subtle borders
- **Text**: `#0F172A` - Primary text color
- **Text Secondary**: `#64748B` - Secondary text
- **Text Muted**: `#94A3B8` - Muted/disabled text

## CSS Variables (CSS Custom Properties)

All colors are defined as CSS variables in `:root`. Use them throughout your stylesheets:

```css
/* Color variables */
--clr-primary: #6366F1;
--clr-primary-light: #E0E7FF;
--clr-primary-dark: #4F46E5;
--clr-success: #10B981;
--clr-warning: #F59E0B;
--clr-danger: #EF4444;
--clr-info: #0EA5E9;

/* Gradient variables */
--gradient-primary: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
--gradient-accent: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
--gradient-success: linear-gradient(135deg, #10B981 0%, #14B8A6 100%);

/* Shadow variables */
--shadow-sm: 0 2px 4px 0 rgba(0,0,0,.05), 0 1px 2px -1px rgba(0,0,0,.04);
--shadow-md: 0 6px 12px -2px rgba(0,0,0,.08), 0 2px 4px -2px rgba(0,0,0,.04);
--shadow-lg: 0 12px 24px -4px rgba(0,0,0,.1), 0 4px 8px -2px rgba(0,0,0,.04);
--shadow-xl: 0 20px 40px -8px rgba(0,0,0,.12), 0 8px 16px -4px rgba(0,0,0,.06);
--shadow-indigo: 0 6px 20px -4px rgba(99,102,241,.25);
--shadow-modal: 0 25px 50px -12px rgba(0,0,0,.15);
```

## Component Usage

### Buttons
```html
<!-- Primary Button -->
<button class="btn btn-primary">Primary Action</button>

<!-- Secondary Button -->
<button class="btn btn-light">Secondary Action</button>

<!-- Danger Button -->
<button class="btn btn-danger">Delete</button>

<!-- Success Button -->
<button class="btn btn-success">Save</button>
```

### Cards
```html
<!-- Content Card -->
<div class="content-card">
  <div class="content-card-header">
    <h2>Card Title</h2>
  </div>
  <div class="p-4">
    Card content here
  </div>
</div>
```

### Status Badges
```html
<!-- Active Status -->
<span class="status-badge status-active">
  <span></span> Active
</span>

<!-- Inactive Status -->
<span class="status-badge status-inactive">
  <span></span> Inactive
</span>

<!-- Job Status -->
<span class="job-status status-completed">
  <span class="status-dot"></span> Completed
</span>
```

### Stats and Metrics
```html
<!-- Stat Card with Icon -->
<div class="stat-card">
  <div class="stat-icon indigo">
    <i class="bi bi-graph-up"></i>
  </div>
  <div class="stat-info">
    <div class="stat-label">Total Revenue</div>
    <div class="stat-value">$48,650</div>
  </div>
</div>
```

## Icons and Avatars

### Avatar Circles
```html
<!-- Default Avatar -->
<div class="avatar-circle">AD</div>

<!-- Violet Avatar -->
<div class="avatar-circle violet">JD</div>

<!-- Success Avatar -->
<div class="avatar-circle success">SK</div>

<!-- Warning Avatar -->
<div class="avatar-circle warning">MK</div>
```

### Icon Circles
```html
<!-- Primary Icon Circle -->
<div class="avatar-icon-circle">
  <i class="bi bi-person"></i>
</div>

<!-- Accent Icon Circle -->
<div class="avatar-icon-circle accent">
  <i class="bi bi-star"></i>
</div>
```

## Spacing & Layout

### Standard Spacing Scale
- `4px` - xs (smallest)
- `8px` - sm (small)
- `12px` - md (medium)
- `16px` - lg (large)
- `20px` - xl (extra large)
- `24px` - 2xl (2x extra large)
- `28px` - 3xl (3x extra large)
- `32px` - 4xl (4x extra large)

### Border Radius Scale
```css
--radius-sm:  6px;    /* Small rounded corners */
--radius-md:  10px;   /* Medium rounded corners */
--radius-lg:  14px;   /* Large rounded corners */
--radius-xl:  18px;   /* Extra large rounded corners */
--radius-2xl: 24px;   /* 2x extra large rounded corners */
--radius-full: 9999px; /* Perfect circles */
```

## Shadows

Use shadows to create depth and hierarchy:

```css
/* Subtle shadow for cards */
box-shadow: var(--shadow-sm);

/* Medium shadow for hover states */
box-shadow: var(--shadow-md);

/* Large shadow for prominent elements */
box-shadow: var(--shadow-lg);

/* Extra large shadow for modals */
box-shadow: var(--shadow-xl);

/* Colored shadows for brand elements */
box-shadow: var(--shadow-indigo);
box-shadow: var(--shadow-accent);

/* Modal shadow */
box-shadow: var(--shadow-modal);
```

## Typography

### Font Stack
```css
font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

### Font Weights
- `400` - Regular
- `500` - Medium
- `600` - Semibold
- `700` - Bold
- `800` - Extra Bold

### Text Classes
```html
<!-- Heading -->
<h1 class="display-1">Page Title</h1>

<!-- Subheading -->
<h2 class="h2">Section Title</h2>

<!-- Secondary Text -->
<p class="text-secondary">Secondary information</p>

<!-- Muted Text -->
<span class="text-muted">Muted information</span>
```

## Transitions & Animations

### Standard Transition
```css
transition: 0.18s ease;
```

### Specific Transitions
- Color transitions: `0.18s ease`
- Transform transitions: `0.18s ease`
- Shadow transitions: `0.18s ease`

### Hover Effects
```css
/* Standard hover - slight lift */
transform: translateY(-1px);
box-shadow: var(--shadow-md);

/* Button hover - more lift */
transform: translateY(-2px);
box-shadow: var(--shadow-lg);
```

## Responsive Design

### Breakpoints
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: < 768px
- **Small Mobile**: < 640px

### Media Query Examples
```css
/* Tablet and below */
@media (max-width: 1199px) {
  .dashboard-stats-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile only */
@media (max-width: 768px) {
  .sidebar { width: 60px; } /* Collapse sidebar on mobile */
}
```

## Creating New Components

When creating new components, follow these guidelines:

1. **Use CSS Variables**: Always use `var()` for colors instead of hardcoding hex values
2. **Consistent Spacing**: Use the spacing scale (4px, 8px, 12px, 16px, 20px, 24px, 32px)
3. **Proper Border Radius**: Use predefined radius variables
4. **Shadow Hierarchy**: Use appropriate shadow levels for depth
5. **Transitions**: Add smooth transitions for interactive elements
6. **Accessibility**: Ensure proper color contrast and focus states

### Example Component
```css
.new-component {
    background: var(--clr-surface);
    border: 1px solid var(--clr-border);
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: 0 4px 12px -2px rgba(0,0,0,.05);
    transition: box-shadow 0.18s ease, border-color 0.18s ease;
}

.new-component:hover {
    border-color: rgba(99,102,241,.15);
    box-shadow: 0 8px 20px -4px rgba(0,0,0,.08);
}
```

## Best Practices

1. **Gradient Over Solid**: Use gradients for primary buttons and active states
2. **Light Backgrounds**: Use light color variations for backgrounds and inactive states
3. **Consistent Icons**: Use Bootstrap Icons (bi) with appropriate sizing
4. **Status Indicators**: Use colored badges and dots for status information
5. **Animations**: Keep animations subtle and purposeful
6. **Accessibility**: Ensure text has sufficient contrast against background colors
7. **Mobile First**: Design for mobile then enhance for larger screens

## Customization

To customize the theme globally, update the CSS variables in the `:root` selector in `public/css/app.css`. Changes will propagate throughout the entire application.

For component-specific overrides, create targeted CSS rules while still respecting the established pattern of using variables and maintaining visual consistency.

## Color Combination Guidelines

### Recommended Combinations
- Primary + Secondary: Indigo + Violet (sidebar + header)
- Success + Info: Green + Blue (completed + in-progress)
- Warning + Danger: Orange + Red (pending + error)
- Accent + Primary: Pink + Indigo (highlights + main)

### Avoid
- Using text color on same-hue backgrounds
- Mixing more than 3 primary colors in one interface section
- Nesting more than 2 levels of card-on-card

## Support for Dark Mode (Future)

The theme is structured to easily support dark mode in the future:

1. Define dark mode CSS variables in a media query
2. Update colors for proper contrast in dark backgrounds
3. Adjust shadows and highlights for dark theme

---

**Last Updated**: 2026-08-13
**Version**: 2.0
**License**: SalonPro Management System
