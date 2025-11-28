<?php

namespace App\Infrastructure\DIContainer;

//  Import tất cả các UseCase cần thiết
use App\Application\UseCases\CheckRoomAvailableUseCase;
use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomImageUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\DetailUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\GetStorageHealthCheckUseCase;
use App\Application\UseCases\GetStorageInfoUseCase;
use App\Application\UseCases\SetPrimaryImageUseCase;
use App\Application\UseCases\SwitchStorageProviderUseCase;
use App\Application\UseCases\UpdateImageDisplayOrderUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\UseCases\CreateRoomUseCase;
use App\Application\UseCases\UpdateRoomUseCase;
use App\Application\UseCases\DeleteRoomUseCase;
use App\Application\UseCases\GetRoomUseCase;
use App\Application\UseCases\GetAllRoomUseCase;
use App\Application\UseCases\FilterRoomByRoomNumberUseCase;
use App\Application\UseCases\FilterRoomByStatusUseCase;
use App\Application\UseCases\CreateBookingUseCase;
use App\Application\UseCases\DeleteBookingUseCase;
use App\Application\UseCases\UpdateBookingUseCase;
use App\Application\UseCases\GetBookingUseCase;
use App\Application\UseCases\GetAllBookingUseCase;
use App\Application\UseCases\FilterBookingByCheckInDateUseCase;
use App\Application\UseCases\FilterBookingByCheckOutDateUseCase;
use App\Application\UseCases\FilterBookingByCodeUseCase;
use App\Application\UseCases\FilterBookingByDayByDayUseCase;
use App\Application\UseCases\FilterBookingByEmailUseCase;
use App\Application\UseCases\FilterBookingByName;
use App\Application\UseCases\FilterBookingByPhoneUseCase;
use App\Application\UseCases\FilterBookingByStatus;
use App\Application\UseCases\GetAllRoomsWithDetailsUseCase;
use App\Application\UseCases\GetRoomWithDetailsUseCase;
use App\Application\UseCases\UploadRoomImagesUseCase;
use App\Application\UseCases\SearchAvailableRoomsUseCase;
use App\Application\UseCases\SearchRoomsWithDatesUseCase;
use App\Application\Validators\RoomTypeValidator;
use App\Application\Validators\RoomValidator;
use App\Application\Validators\BookingValidator;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Domain\Interfaces\Services\StorageConfigInterface;
use App\Infrastructure\Services\ImageUploadFacade;

/**
 * UseCaseProvider - Nơi đăng ký (bind) tất cả các UseCase vào DI Container
 */
class UseCaseProvider
{
    /**
     * Hàm khởi tạo: gọi các nhóm UseCase để đăng ký
     */
    public static function register(Container $container): void
    {
        // Nhóm UseCase liên quan đến RoomType
        self::registerRoomTypeUseCases($container);

        // Có thể thêm các nhóm UseCase khác sau này
        // RoomImage Use Cases
        self::registerRoomImageUseCases($container);

        // Add more use case groups here
        // self::registerUserUseCases($container);
        // self::registerBookingUseCases($container);
        self::registerRoomUseCases($container);
        self::registerBookingUseCases($container);
    }

    /**
     * Đăng ký các UseCase thuộc nhóm RoomType
     */
    private static function registerRoomTypeUseCases(Container $container): void
    {
        // Tạo loại phòng mới
        $container->bind(CreateRoomTypeUseCase::class, function (Container $c) {
            return new CreateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        // Lấy thông tin chi tiết 1 loại phòng
        $container->bind(GetRoomTypeUseCase::class, function (Container $c) {
            return new GetRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lấy danh sách tất cả loại phòng
        $container->bind(GetAllRoomTypesUseCase::class, function (Container $c) {
            return new GetAllRoomTypesUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Cập nhật loại phòng
        $container->bind(UpdateRoomTypeUseCase::class, function (Container $c) {
            return new UpdateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        // Xóa loại phòng
        $container->bind(DeleteRoomTypeUseCase::class, function (Container $c) {
            return new DeleteRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lọc loại phòng theo sức chứa
        $container->bind(FilterRoomTypesByCapacityUseCase::class, function (Container $c) {
            return new FilterRoomTypesByCapacityUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lọc loại phòng theo khoảng giá
        $container->bind(FilterRoomTypesByPriceRangeUseCase::class, function (Container $c) {
            return new FilterRoomTypesByPriceRangeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        // Lấy danh sách loại phòng nổi bật (mới thêm)
        // Lấy danh sách loại phòng nổi bật - tạm thời chưa đăng ký vì lớp UseCase chưa tồn tại
    }
}
    }

    private static function registerRoomUseCases(Container $container): void{
        $container->bind(CreateRoomUseCase::class, function (Container $c) {
            return new CreateRoomUseCase(
                $c->make(RoomRepositoryInterface::class),
                $c->make(RoomValidator::class)
            );
        });

        $container->bind(GetRoomUseCase::class, function (Container $c) {
            return new GetRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(GetAllRoomUseCase::class, function (Container $c) {
            return new GetAllRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(UpdateRoomUseCase::class, function (Container $c) {
            return new UpdateRoomUseCase(
                $c->make(RoomRepositoryInterface::class),
                $c->make(RoomValidator::class)
            );
        });

        $container->bind(DeleteRoomUseCase::class, function (Container $c) {
            return new DeleteRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomByStatusUseCase::class, function (Container $c) {
            return new FilterRoomByStatusUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomByRoomNumberUseCase::class, function (Container $c) {
            return new FilterRoomByRoomNumberUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(GetAllRoomsWithDetailsUseCase::class, function (Container $c) {
            return new GetAllRoomsWithDetailsUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(GetRoomWithDetailsUseCase::class, function (Container $c) {
            return new GetRoomWithDetailsUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });
        $container->bind(DetailUseCase::class, function (Container $c) {
            return new DetailUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(SearchAvailableRoomsUseCase::class, function (Container $c) {
            return new SearchAvailableRoomsUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(SearchRoomsWithDatesUseCase::class, function (Container $c) {
            return new SearchRoomsWithDatesUseCase(
                $c->make(RoomRepositoryInterface::class),
                $c->make(BookingRepositoryInterface::class)
            );
        });
    }
    private static function registerBookingUseCases(Container $container): void{
        $container->bind(CreateBookingUseCase::class, function (Container $c) {
            return new CreateBookingUseCase(
                $c->make(BookingRepositoryInterface::class),
                $c->make(BookingValidator::class)
            );
        });

        $container->bind(DeleteBookingUseCase::class, function (Container $c) {
            return new DeleteBookingUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(GetAllBookingUseCase::class, function (Container $c) {
            return new GetAllBookingUseCase(
                $c->make(BookingRepositoryInterface::class),
            );
        });
        $container->bind(GetBookingUseCase::class, function (Container $c) {
            return new GetBookingUseCase(
                $c->make(BookingRepositoryInterface::class),
            );
        });
        $container->bind(UpdateBookingUseCase::class, function (Container $c) {
            return new UpdateBookingUseCase(
                $c->make(BookingRepositoryInterface::class),
                $c->make(BookingValidator::class)
            );
        });
        $container->bind(FilterBookingByCheckInDateUseCase::class, function (Container $c) {
            return new FilterBookingByCheckInDateUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByCodeUseCase::class, function (Container $c) {
            return new FilterBookingByCodeUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByDayByDayUseCase::class, function (Container $c) {
            return new FilterBookingByDayByDayUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByEmailUseCase::class, function (Container $c) {
            return new FilterBookingByEmailUsecase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByName::class, function (Container $c) {
            return new FilterBookingByName(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByPhoneUseCase::class, function (Container $c) {
            return new FilterBookingByPhoneUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(FilterBookingByStatus::class, function (Container $c) {
            return new FilterBookingByStatus(
                $c->make(BookingRepositoryInterface::class)
            );
        });
        $container->bind(CheckRoomAvailableUseCase::class, function (Container $c) {
            return new CheckRoomAvailableUseCase(
                $c->make(BookingRepositoryInterface::class)
            );
        });

    }


    private static function registerRoomImageUseCases(Container $container): void
    {
        // Upload Room Images Use Case
        $container->bind(UploadRoomImagesUseCase::class, function (Container $c) {
            return new UploadRoomImagesUseCase(
                $c->make(RoomImageRepositoryInterface::class),
                $c->make(ImageUploadFacade::class),
                $c->make(RoomRepositoryInterface::class)
            );
        });

        // Set Primary Image Use Case
        $container->bind(SetPrimaryImageUseCase::class, function (Container $c) {
            return new SetPrimaryImageUseCase(
                $c->make(RoomImageRepositoryInterface::class)
            );
        });

        // Update Image Display Order Use Case
        $container->bind(UpdateImageDisplayOrderUseCase::class, function (Container $c) {
            return new UpdateImageDisplayOrderUseCase(
                $c->make(RoomImageRepositoryInterface::class)
            );
        });

        // Delete Room Image Use Case
        $container->bind(DeleteRoomImageUseCase::class, function (Container $c) {
            return new DeleteRoomImageUseCase(
                $c->make(RoomImageRepositoryInterface::class),
                $c->make(ImageUploadFacade::class)
            );
        });

        // Get Storage Health Check Use Case
        $container->bind(GetStorageHealthCheckUseCase::class, function (Container $c) {
            return new GetStorageHealthCheckUseCase(
                $c->make(ImageUploadFacade::class)
            );
        });

        // Get Storage Info Use Case
        $container->bind(GetStorageInfoUseCase::class, function (Container $c) {
            return new GetStorageInfoUseCase(
                $c->make(ImageUploadFacade::class),
                $c->make(RoomImageRepositoryInterface::class)
            );
        });

        // Switch Storage Provider Use Case
        $container->bind(SwitchStorageProviderUseCase::class, function (Container $c) {
            return new SwitchStorageProviderUseCase(
                $c->make(StorageConfigInterface::class)
            );
        });
    }
}
