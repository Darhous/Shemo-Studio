"""WCAG contrast checks for Phase 14 Cinematic Noir tokens.

Run from the repository root:
    python design/mockups/phase14-contrast-check.py
"""

TOKENS = {
    "obsidian": "#0E0F12",
    "graphite": "#1C1E22",
    "graphite_2": "#23262B",
    "ivory": "#F5F2EC",
    "muted": "#A7A39B",
    "ember": "#FF5A2C",
    "border": "#33363C",
}

PAIRS = [
    ("ivory", "obsidian", "Body and hero text on page background"),
    ("muted", "obsidian", "Muted body text on page background"),
    ("ember", "obsidian", "Accent text and inline links on page background"),
    ("ivory", "graphite", "Card heading text on surface"),
    ("muted", "graphite", "Card body text on surface"),
    ("ember", "graphite", "Accent labels on surface"),
    ("obsidian", "ember", "Primary CTA text on Ember button"),
    ("ivory", "ember", "Rejected: Ivory text on Ember button"),
    ("ivory", "graphite_2", "Secondary CTA text on raised surface"),
    ("ember", "graphite_2", "Focus/interactive accent on raised surface"),
]


def _channel_to_linear(channel):
    value = channel / 255
    if value <= 0.03928:
        return value / 12.92
    return ((value + 0.055) / 1.055) ** 2.4


def relative_luminance(hex_color):
    color = hex_color.lstrip("#")
    red = _channel_to_linear(int(color[0:2], 16))
    green = _channel_to_linear(int(color[2:4], 16))
    blue = _channel_to_linear(int(color[4:6], 16))
    return (0.2126 * red) + (0.7152 * green) + (0.0722 * blue)


def contrast_ratio(foreground, background):
    lum_1 = relative_luminance(foreground)
    lum_2 = relative_luminance(background)
    lighter = max(lum_1, lum_2)
    darker = min(lum_1, lum_2)
    return (lighter + 0.05) / (darker + 0.05)


def aa_result(ratio):
    normal = "PASS" if ratio >= 4.5 else "FAIL"
    large = "PASS" if ratio >= 3 else "FAIL"
    return normal, large


if __name__ == "__main__":
    print("Phase 14 Cinematic Noir contrast results")
    print("WCAG AA thresholds: normal text 4.5:1, large text/UI 3:1\n")
    for foreground, background, usage in PAIRS:
        ratio = contrast_ratio(TOKENS[foreground], TOKENS[background])
        normal, large = aa_result(ratio)
        print(
            f"{foreground:10s} on {background:10s} "
            f"{ratio:5.2f}:1  normal={normal:4s} large/UI={large:4s}  {usage}"
        )
