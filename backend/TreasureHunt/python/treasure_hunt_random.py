import random
from treasure_hunt import TreasureHunt

def generate_random_dice(turns: int) -> str:
    """指定ターン分のランダムなサイコロ出目を文字列として返す"""
    dice_chars = ['1','2','3','4','5','6']
    result = ""
    for _ in range(turns):
        result += random.choice(dice_chars)
    return result

if __name__ == "__main__":
    # 実行ターン数をランダムで決定（10〜100）
    random_turns = random.randint(10, 100)

    # ランダムなサイコロの出目を生成
    dice_sequence = generate_random_dice(random_turns)

    # TreasureHunt インスタンス生成
    game = TreasureHunt()

    # 実行
    output = game.execute(1, dice_sequence)

    # 結果表示
    print("=== Treasure Hunt Result ===")
    print(f"ターン数（指定/実際）: {random_turns} / {game.turn}")
    print(f"所持ゴールド: {game.gold}")
    print(f"最終位置: {game.position}")
    print(f"行動記録: {game.record}")
    print("=============================")
