class Land:
    LandStart = 0
    LandGoal = 99
    LandA = 1
    LandB = 2
    LandC = 3
    LandD = 4
    LandE = 5

    def __init__(self, name: str):
        self.name = name

    def get_dice(self, arr_input: str, index: int):
        """ターンでのダイス目取得"""
        return arr_input[index]

    def get_next_position(self, dice: str, gold: int):
        """次の島への移動。返り値に次の位置とゴールドを返す"""
        return Land.LandStart, gold

    def get_turn_record(self, dice: str):
        return f"{self.name} {dice}"

    def is_even(self, dice: int):
        return dice % 2 == 0


class LandStart(Land):
    def get_next_position(self, dice: str, gold: int):
        if dice == '1':
            return Land.LandA, gold
        elif dice == '2':
            return Land.LandB, gold
        elif dice == '6':
            return Land.LandC, gold
        return Land.LandStart, gold


class LandA(Land):
    def get_next_position(self, dice: str, gold: int):
        if dice == '3':
            return Land.LandB, gold
        elif dice == '4':
            return Land.LandC, gold
        gold += 100
        return Land.LandStart, gold


class LandB(Land):
    def get_next_position(self, dice: str, gold: int):
        if self.is_even(int(dice)):
            return Land.LandE, gold
        return Land.LandD, gold


class LandC(Land):
    def get_dice(self, arr_input: str, index: int):
        if index + 1 < len(arr_input):
            # サイコロを2回分まとめて取得
            return arr_input[index] + arr_input[index + 1]
        else:
            # 最後のターン付近で1文字しか残っていない場合は2回目を同じ目として取得
            return arr_input[index] + arr_input[index]


    def get_next_position(self, dice: str, gold: int):
        total = int(dice[0]) + int(dice[1])
        if self.is_even(total):
            gold += 100
            return Land.LandE, gold
        gold += 200
        return Land.LandD, gold


class LandD(Land):
    def get_next_position(self, dice: str, gold: int):
        if dice in ('4', '5'):
            return Land.LandE, gold
        return Land.LandStart, gold


class LandE(Land):
    def get_next_position(self, dice: str, gold: int):
        if dice == '6' and gold >= 500:
            return Land.LandGoal, gold
        gold += 100
        return Land.LandC, gold


class LandFactory:
    @staticmethod
    def get_land(position: int):
        return {
            Land.LandA: LandA("A"),
            Land.LandB: LandB("B"),
            Land.LandC: LandC("C"),
            Land.LandD: LandD("D"),
            Land.LandE: LandE("E"),
        }.get(position, LandStart("S"))


class TreasureHunt:
    def __init__(self):
        self.record = ""
        self.gold = 0
        self.position = Land.LandStart
        self.turn = 0

    def execute(self, number: int, arr_input: str):
        index = 0
        while index < len(arr_input):
            self.turn += 1
            land = LandFactory.get_land(self.position)

            # ダイス目を取得
            dice = land.get_dice(arr_input, index)

            # 記録
            prefix = "" if not self.record else ", "
            self.record += prefix + land.get_turn_record(dice)

            # 次の位置とゴールドを取得
            self.position, self.gold = land.get_next_position(dice, self.gold)

            # ゴール判定
            if self.position == Land.LandGoal:
                self.record += ", G"
                break

            # LandC は2文字使うので index を調整
            index += 2 if isinstance(land, LandC) else 1

        return f'{number}, "{arr_input}", {self.turn}, {self.gold}, "{self.record}"'
