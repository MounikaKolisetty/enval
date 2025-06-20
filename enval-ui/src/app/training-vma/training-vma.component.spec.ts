import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TrainingVmaComponent } from './training-vma.component';

describe('TrainingVmaComponent', () => {
  let component: TrainingVmaComponent;
  let fixture: ComponentFixture<TrainingVmaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TrainingVmaComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TrainingVmaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
